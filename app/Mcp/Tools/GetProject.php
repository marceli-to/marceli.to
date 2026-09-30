<?php

namespace App\Mcp\Tools;

use App\Mcp\Support\Projects;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Name('get_project')]
#[Description(<<<'TEXT'
Get all details of one portfolio project by its slug: title, website url, description, client, agency, year, screenshot (asset path and public URL), published state and the Control Panel edit link.
If several projects share a slug, the error lists their ids; call again with "id" instead.
TEXT)]
#[IsReadOnly]
class GetProject extends Tool
{
    public function handle(Request $request): Response|ResponseFactory
    {
        $input = $request->validate([
            'slug' => ['required_without:id', 'nullable', 'string'],
            'id' => ['nullable', 'string'],
        ]);

        $entry = Projects::find($input['slug'] ?? null, $input['id'] ?? null);

        return Response::structured(Projects::details($entry));
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'slug' => $schema->string()->description('The project slug, as returned by list_projects.'),
            'id' => $schema->string()->description('The project id. Only needed when a slug is ambiguous.'),
        ];
    }
}
