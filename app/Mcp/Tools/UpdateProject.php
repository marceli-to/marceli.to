<?php

namespace App\Mcp\Tools;

use App\Mcp\Support\Projects;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsIdempotent;

#[Name('update_project')]
#[Description(<<<'TEXT'
Update fields of an existing portfolio project, identified by its slug (see list_projects). Only the fields you pass are changed; everything else is kept.
Updatable: url, title, description, year, client, agency (pass an empty string to remove it), image (an asset path from upload_screenshot). The slug and the published state cannot be changed here; use publish_project to publish.
Changes to a published project are live immediately, so show the user the old and new values and get a confirmation before calling this tool.
TEXT)]
#[IsIdempotent]
class UpdateProject extends Tool
{
    public const FIELDS = ['url', 'title', 'description', 'year', 'client', 'agency', 'image'];

    public function handle(Request $request): Response|ResponseFactory
    {
        if (is_int($request->get('year'))) {
            $request->merge(['year' => (string) $request->get('year')]);
        }

        $input = $request->validate([
            'slug' => ['required_without:id', 'nullable', 'string'],
            'id' => ['nullable', 'string'],
            ...Projects::rules(creating: false),
        ], Projects::messages());

        $changes = Arr::only($input, static::FIELDS);

        if ($changes === []) {
            throw ValidationException::withMessages(['fields' => 'Pass at least one field to change: '.implode(', ', static::FIELDS).'.']);
        }

        $entry = Projects::find($input['slug'] ?? null, $input['id'] ?? null);
        $createdTerms = Projects::fill($entry, $changes);
        $entry->save();

        Projects::logWrite('update_project', Projects::slugOf($entry), ['id' => $entry->id(), 'fields' => array_keys($changes), 'new_terms' => $createdTerms]);

        return Response::structured([
            'message' => 'Updated '.implode(', ', array_keys($changes)).($entry->published() ? '. The project is published, so the change is live.' : '. The project is still a draft.'),
            'new_clients_created' => $createdTerms,
            'project' => Projects::details($entry),
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'slug' => $schema->string()->description('The project slug, as returned by list_projects.'),
            'id' => $schema->string()->description('The project id. Only needed when a slug is ambiguous.'),
            'url' => $schema->string()->description('Full URL of the live website.'),
            'title' => $schema->string()->description('Project title.'),
            'description' => $schema->string()->description('One or two sentences in German.'),
            'year' => $schema->string()->description('e.g. "2025" or "2016 – 2020".'),
            'client' => $schema->string()->description('Client name; reuse an existing name from list_projects when possible.'),
            'agency' => $schema->string()->description('Concept/design agency; empty string removes it.'),
            'image' => $schema->string()->description('Asset path returned by upload_screenshot.'),
        ];
    }
}
