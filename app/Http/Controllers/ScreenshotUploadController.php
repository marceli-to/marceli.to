<?php

namespace App\Http\Controllers;

use App\Mcp\Support\Projects;
use App\Mcp\Support\Screenshots;
use App\Mcp\Tools\GetScreenshotUploadUrl;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

/**
 * Receives a screenshot as the raw request body at a signed URL from get_screenshot_upload_url,
 * so local files can be uploaded with curl instead of being base64-encoded into a tool call.
 */
class ScreenshotUploadController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $websiteUrl = (string) $request->query('website_url');

        // Signed URLs are single use.
        if (! Cache::add('mcp-screenshot-upload:'.$request->query('nonce'), true, now()->addMinutes(GetScreenshotUploadUrl::TTL_MINUTES + 1))) {
            return response()->json(['error' => 'This upload URL has already been used. Call get_screenshot_upload_url again.'], 410);
        }

        try {
            $binary = $request->getContent();

            if ($binary === '') {
                throw ValidationException::withMessages(['image' => 'The request body is empty. Send the file with curl --data-binary @"FILE".']);
            }

            Screenshots::assertSize(strlen($binary), 'image');
            $asset = Screenshots::store($binary, $websiteUrl);
        } catch (ValidationException $e) {
            // Let the caller retry with the same URL after fixing the file.
            Cache::forget('mcp-screenshot-upload:'.$request->query('nonce'));

            return response()->json(['error' => collect($e->errors())->flatten()->implode(' ')], 422);
        }

        Projects::logWrite('upload_screenshot', null, [
            'path' => $asset->path(),
            'source' => 'upload_url',
        ]);

        return response()->json([
            'message' => 'Screenshot stored. Pass "image": "'.$asset->path().'" to create_project or update_project.',
            'image' => $asset->path(),
            'url' => $asset->absoluteUrl(),
            'width' => $asset->width(),
            'height' => $asset->height(),
        ]);
    }
}
