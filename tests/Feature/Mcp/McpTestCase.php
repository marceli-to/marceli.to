<?php

namespace Tests\Feature\Mcp;

use App\Mcp\Support\Screenshots;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Bootstrap\LoadConfiguration;
use Statamic\Facades\User;
use Tests\TestCase;

/**
 * Runs MCP tests against a throwaway copy of content/, a temporary assets disk,
 * search index and MCP log, so real content is never written to.
 */
abstract class McpTestCase extends TestCase
{
    protected string $sandbox;

    protected string $realContentFingerprint;

    /**
     * Point Statamic's content stores, the assets disk, search index and MCP log at a sandbox
     * before the framework boots, so the Stache never sees the real content.
     */
    public function createApplication()
    {
        $this->sandbox = realpath(sys_get_temp_dir()).'/marcelito-mcp-tests-'.uniqid();
        $basePath = Application::inferBasePath();

        (new Filesystem)->copyDirectory($basePath.'/content', $this->sandbox.'/content');
        (new Filesystem)->ensureDirectoryExists($this->sandbox.'/assets/projects');

        $app = require $basePath.'/bootstrap/app.php';

        $app->afterBootstrapping(LoadConfiguration::class, function ($app) use ($basePath) {
            $stores = (require $basePath.'/vendor/statamic/cms/config/stache.php')['stores'];

            foreach ($stores as $key => $store) {
                if (isset($store['directory']) && str_starts_with($store['directory'], $basePath.'/content')) {
                    $stores[$key]['directory'] = str_replace($basePath.'/content', $this->sandbox.'/content', $store['directory']);
                }
            }

            $app['config']->set([
                'statamic.stache.stores' => $stores,
                'filesystems.disks.assets.root' => $this->sandbox.'/assets',
                'statamic.search.drivers.local.path' => $this->sandbox.'/search',
                'logging.channels.mcp.driver' => 'single',
                'logging.channels.mcp.path' => $this->sandbox.'/mcp.log',
            ]);
        });

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }

    protected function setUp(): void
    {
        $this->realContentFingerprint = $this->fingerprint();

        parent::setUp();

        Screenshots::$resolveHostUsing = fn () => ['93.184.215.14'];
    }

    protected function tearDown(): void
    {
        Screenshots::$resolveHostUsing = null;
        (new Filesystem)->deleteDirectory($this->sandbox);

        parent::tearDown();

        $this->assertSame($this->realContentFingerprint, $this->fingerprint(), 'A test wrote to the real content, users, assets or MCP log.');
    }

    protected function superUser()
    {
        return User::make()->id('mcp-test-user')->email('mcp-test@example.com')->makeSuper();
    }

    protected function mcpLog(): string
    {
        return (new Filesystem)->exists($this->sandbox.'/mcp.log') ? (new Filesystem)->get($this->sandbox.'/mcp.log') : '';
    }

    protected function sandboxEntryFiles(): array
    {
        return (new Filesystem)->files($this->sandbox.'/content/collections/projects/de');
    }

    /**
     * A generated PNG/JPEG screenshot of the given size.
     */
    protected function image(int $width, int $height, string $format = 'png'): string
    {
        $image = imagecreatetruecolor($width, $height);
        imagefill($image, 0, 0, imagecolorallocate($image, 233, 67, 100));

        ob_start();
        $format === 'png' ? imagepng($image) : ($format === 'webp' ? imagewebp($image) : ($format === 'gif' ? imagegif($image) : imagejpeg($image)));

        return ob_get_clean();
    }

    /**
     * Files, sizes and modification times of everything the tests must never touch.
     */
    private function fingerprint(): string
    {
        $base = Application::inferBasePath();

        $files = collect(['content', 'users', 'public/assets', 'storage/statamic/search'])
            ->filter(fn ($dir) => is_dir("{$base}/{$dir}"))
            ->flatMap(fn ($dir) => (new Filesystem)->allFiles("{$base}/{$dir}", true))
            ->map(fn ($file) => $file->getPathname())
            ->merge(glob("{$base}/storage/logs/mcp*.log") ?: []);

        return $files
            ->map(fn ($path) => $path.':'.filesize($path).':'.filemtime($path))
            ->sort()
            ->implode("\n");
    }
}
