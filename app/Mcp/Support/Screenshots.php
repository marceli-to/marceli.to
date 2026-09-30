<?php

namespace App\Mcp\Support;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;
use Intervention\Image\Encoders\JpegEncoder;
use Intervention\Image\Encoders\PngEncoder;
use Intervention\Image\ImageManager;
use Statamic\Contracts\Assets\Asset;
use Statamic\Facades\AssetContainer;
use Throwable;

/**
 * Stores project screenshots following the conventions of the existing ones:
 * 3:2, at most 2400px wide, named after the website's domain (e.g. "example.ch.png").
 */
class Screenshots
{
    public const MAX_BYTES = 10 * 1024 * 1024;

    public const MIN_WIDTH = 1200;

    public const MAX_WIDTH = 2400;

    public const MIME_TYPES = ['image/jpeg', 'image/png', 'image/webp'];

    /**
     * Overrides DNS resolution for the private-network check (used in tests).
     *
     * @var (\Closure(string): array<int, string>)|null
     */
    public static ?\Closure $resolveHostUsing = null;

    /**
     * Decode base64 image data (a raw string or a data URI).
     */
    public static function fromBase64(string $data): string
    {
        $data = preg_replace('/^data:[^;,]+;base64,/', '', trim($data));
        $binary = base64_decode(preg_replace('/\s+/', '', $data), true);

        if ($binary === false || $binary === '') {
            throw ValidationException::withMessages(['data' => 'The data is not valid base64. Send the file contents base64-encoded, without line breaks.']);
        }

        static::assertSize(strlen($binary), 'data');

        return $binary;
    }

    /**
     * Download an image from a public http(s) URL.
     */
    public static function fromUrl(string $url): string
    {
        static::assertPublicUrl($url);

        try {
            $response = Http::timeout(20)
                ->withOptions(['allow_redirects' => false])
                ->withUserAgent('marceli.to MCP screenshot fetcher')
                ->get($url);
        } catch (ConnectionException $e) {
            throw ValidationException::withMessages(['image_url' => "The image could not be downloaded: {$e->getMessage()}"]);
        }

        if ($response->redirect()) {
            throw ValidationException::withMessages(['image_url' => 'The image URL redirects to '.$response->header('Location').'. Call again with that direct URL.']);
        }

        if (! $response->successful()) {
            throw ValidationException::withMessages(['image_url' => "The image URL returned HTTP {$response->status()}."]);
        }

        static::assertSize(strlen($response->body()), 'image_url');

        return $response->body();
    }

    /**
     * Check the image, normalise it to the site's conventions and store it as an asset.
     */
    public static function store(string $binary, string $websiteUrl): Asset
    {
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->buffer($binary);

        if (! in_array($mime, static::MIME_TYPES, true)) {
            throw ValidationException::withMessages(['image' => "The file is {$mime}. Only JPEG, PNG and WebP screenshots are accepted."]);
        }

        try {
            $image = static::manager()->decodeBinary($binary);
        } catch (Throwable) {
            throw ValidationException::withMessages(['image' => 'The image could not be read. It may be corrupt or truncated.']);
        }

        if ($image->width() < static::MIN_WIDTH) {
            throw ValidationException::withMessages(['image' => "The screenshot is {$image->width()}px wide. It must be at least ".static::MIN_WIDTH.'px wide (existing ones are 1600–3200px).']);
        }

        // Crop to 3:2: keep the top of tall (full-page) screenshots, the centre of wide ones.
        $width = $image->width();
        $height = $image->height();
        $targetHeight = (int) round($width * 2 / 3);

        if ($height > $targetHeight) {
            $image->crop($width, $targetHeight);
        } elseif ($height < $targetHeight) {
            $targetWidth = (int) round($height * 3 / 2);
            $image->crop($targetWidth, $height, (int) floor(($width - $targetWidth) / 2), 0);
        }

        $image->scaleDown(width: static::MAX_WIDTH);

        [$encoder, $extension] = $mime === 'image/png'
            ? [new PngEncoder, 'png']
            : [new JpegEncoder(quality: 85), 'jpg'];

        $container = AssetContainer::find(Projects::ASSET_CONTAINER);
        $path = static::availablePath($websiteUrl, $extension);

        $container->disk()->put($path, (string) $image->encode($encoder));

        $asset = $container->makeAsset($path);
        $asset->save();

        return $asset;
    }

    /**
     * "https://www.example.ch/foo" becomes "projects/example.ch.png", or "example.ch-2.png" when taken.
     */
    public static function availablePath(string $websiteUrl, string $extension): string
    {
        $name = Projects::titleFromUrl($websiteUrl);
        $name = trim(preg_replace('/[^a-z0-9.-]+/', '-', $name), '-.') ?: 'screenshot';

        $container = AssetContainer::find(Projects::ASSET_CONTAINER);
        $path = Projects::ASSET_FOLDER."/{$name}.{$extension}";
        $i = 2;

        while ($container->disk()->exists($path)) {
            $path = Projects::ASSET_FOLDER."/{$name}-{$i}.{$extension}";
            $i++;
        }

        return $path;
    }

    protected static function manager(): ImageManager
    {
        $driver = config('statamic.assets.image_manipulation.driver', 'gd') === 'imagick'
            ? new ImagickDriver
            : new GdDriver;

        return new ImageManager($driver);
    }

    protected static function assertSize(int $bytes, string $field): void
    {
        if ($bytes > static::MAX_BYTES) {
            $mb = round($bytes / 1024 / 1024, 1);

            throw ValidationException::withMessages([$field => "The image is {$mb} MB. The limit is 10 MB."]);
        }
    }

    /**
     * Refuse URLs that point at this server or a private network.
     */
    protected static function assertPublicUrl(string $url): void
    {
        $host = parse_url($url, PHP_URL_HOST);
        $ips = match (true) {
            ! $host => [],
            filter_var($host, FILTER_VALIDATE_IP) !== false => [$host],
            static::$resolveHostUsing !== null => (static::$resolveHostUsing)($host),
            default => gethostbynamel($host) ?: [],
        };

        if ($ips === []) {
            throw ValidationException::withMessages(['image_url' => 'The host of the image URL could not be resolved.']);
        }

        foreach ($ips as $ip) {
            if (! filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                throw ValidationException::withMessages(['image_url' => 'The image URL must point to a public server.']);
            }
        }
    }
}
