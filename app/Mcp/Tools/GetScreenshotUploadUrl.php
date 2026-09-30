<?php

namespace App\Mcp\Tools;

use App\Mcp\Support\Projects;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Tool;

#[Name('get_screenshot_upload_url')]
#[Description(<<<'TEXT'
Get a one-time upload URL for a screenshot that is a local file (e.g. an image the user dropped in from their disk). Preferred over upload_screenshot for local files: no base64 needed.

Then upload the file with a shell command, exactly as returned in "curl", replacing FILE with the local path:
  curl -sS --fail-with-body --data-binary @"FILE" "<upload_url>"
The response is JSON with "image" (the asset path to pass as "image" to create_project or update_project), "url", "width" and "height".

The URL is valid for 15 minutes and works once. Same rules as upload_screenshot: JPEG, PNG or WebP, max 10 MB, at least 1200px wide, cropped to 3:2 and scaled to at most 2400px wide, named after the website's domain.
TEXT)]
class GetScreenshotUploadUrl extends Tool
{
    public const TTL_MINUTES = 15;

    public function handle(Request $request): Response|ResponseFactory
    {
        $input = $request->validate([
            'website_url' => ['required', 'string', 'url:http,https', 'max:255'],
        ]);

        // Signed relative to the host, so the signature survives proxies that rewrite the scheme.
        $url = url(URL::temporarySignedRoute('mcp.screenshots.upload', now()->addMinutes(self::TTL_MINUTES), [
            'website_url' => $input['website_url'],
            'nonce' => Str::random(32),
        ], absolute: false));

        Projects::logWrite('get_screenshot_upload_url', null, ['website_url' => $input['website_url']]);

        return Response::structured([
            'upload_url' => $url,
            'curl' => 'curl -sS --fail-with-body --data-binary @"FILE" "'.$url.'"',
            'expires_in_minutes' => self::TTL_MINUTES,
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'website_url' => $schema->string()->description('The live website of the project; used to name the file.')->required(),
        ];
    }
}
