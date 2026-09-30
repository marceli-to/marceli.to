<?php

namespace App\Mcp\Tools;

use App\Mcp\Support\Projects;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Validation\ValidationException;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsIdempotent;

#[Name('publish_project')]
#[Description(<<<'TEXT'
Publish a draft portfolio project so it appears on marceli.to. Only call this when the user explicitly asks to publish; never publish as a side effect of creating or updating.
The project must have a url, description, year, client and screenshot; otherwise the error lists what is missing.
Unpublishing and deleting are not available here; they are done in the Control Panel.
TEXT)]
#[IsIdempotent]
class PublishProject extends Tool
{
    public function handle(Request $request): Response|ResponseFactory
    {
        $input = $request->validate([
            'slug' => ['required_without:id', 'nullable', 'string'],
            'id' => ['nullable', 'string'],
        ]);

        $entry = Projects::find($input['slug'] ?? null, $input['id'] ?? null);

        if ($entry->published()) {
            return Response::structured([
                'message' => "\"{$entry->get('title')}\" is already published.",
                'project' => Projects::details($entry),
            ]);
        }

        if ($missing = Projects::missingForPublishing($entry)) {
            throw ValidationException::withMessages(['project' => 'Cannot publish yet, missing: '.implode(', ', $missing).'. Add them with update_project (screenshots via upload_screenshot).']);
        }

        $entry->published(true)->save();

        Projects::logWrite('publish_project', Projects::slugOf($entry), ['id' => $entry->id()]);

        return Response::structured([
            'message' => "Published \"{$entry->get('title')}\". It is now live on ".url('/').'.',
            'project' => Projects::details($entry),
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'slug' => $schema->string()->description('The project slug, as returned by list_projects.'),
            'id' => $schema->string()->description('The project id. Only needed when a slug is ambiguous.'),
        ];
    }
}
