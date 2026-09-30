<?php

namespace App\Mcp\Tools;

use App\Mcp\Support\Projects;
use App\Mcp\Support\Screenshots;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Tool;

#[Name('upload_screenshot')]
#[Description(<<<'TEXT'
Store a website screenshot for a portfolio project and return its asset path, to pass as "image" to create_project or update_project. Uploading alone does not change any project.

For a local file (e.g. one the user dropped in), prefer get_screenshot_upload_url: it avoids sending large base64 strings.
Otherwise send the image either as base64 "data" together with its "mime_type" and "filename", or as "image_url" to download it from a public URL (fallback).
Always pass "website_url" (the project's live website): the file is named after its domain, e.g. "projects/example.ch.png".

Accepted: JPEG, PNG or WebP, max 10 MB, at least 1200px wide. The image is cropped to 3:2 (keeping the top of full-page screenshots) and scaled down to at most 2400px wide, like the existing screenshots. PNG stays PNG; JPEG and WebP are saved as JPEG.
TEXT)]
class UploadScreenshot extends Tool
{
    public function handle(Request $request): Response|ResponseFactory
    {
        $input = $request->validate([
            'website_url' => ['required', 'string', 'url:http,https', 'max:255'],
            'data' => ['required_without:image_url', 'prohibits:image_url', 'nullable', 'string'],
            'mime_type' => ['required_with:data', 'nullable', 'string', 'in:'.implode(',', Screenshots::MIME_TYPES)],
            'filename' => ['required_with:data', 'nullable', 'string', 'max:255'],
            'image_url' => ['required_without:data', 'nullable', 'string', 'url:http,https', 'max:2048'],
        ], [
            'data.required_without' => 'Send the screenshot as base64 "data" (with mime_type and filename) or as "image_url".',
            'data.prohibits' => 'Send either "data" or "image_url", not both.',
            'mime_type.in' => 'The mime_type must be image/jpeg, image/png or image/webp.',
        ]);

        $binary = filled($input['data'] ?? null)
            ? Screenshots::fromBase64($input['data'])
            : Screenshots::fromUrl($input['image_url']);

        $asset = Screenshots::store($binary, $input['website_url']);

        Projects::logWrite('upload_screenshot', null, [
            'path' => $asset->path(),
            'source' => filled($input['data'] ?? null) ? 'base64:'.($input['filename'] ?? '') : $input['image_url'],
        ]);

        return Response::structured([
            'message' => 'Screenshot stored. Pass "image": "'.$asset->path().'" to create_project or update_project.',
            'image' => $asset->path(),
            'url' => $asset->absoluteUrl(),
            'width' => $asset->width(),
            'height' => $asset->height(),
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'website_url' => $schema->string()->description('The live website of the project; used to name the file.')->required(),
            'data' => $schema->string()->description('Base64-encoded image contents (a data: URI prefix is allowed).'),
            'mime_type' => $schema->string()->enum(Screenshots::MIME_TYPES)->description('Mime type of "data".'),
            'filename' => $schema->string()->description('Original filename of "data", e.g. "screenshot.png".'),
            'image_url' => $schema->string()->description('Public URL to download the image from, if no base64 data is sent.'),
        ];
    }
}
