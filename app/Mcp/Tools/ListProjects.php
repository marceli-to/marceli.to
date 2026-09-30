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
use Statamic\Facades\Taxonomy;

#[Name('list_projects')]
#[Description(<<<'TEXT'
List all portfolio projects on marceli.to, newest year first: id, slug, title, client, year, website url and whether it is published (drafts are not shown on the site).
Also returns every existing client/agency name. When creating or updating a project, reuse one of these names exactly if it refers to the same company, instead of inventing a new spelling.
Use the slug from this list for get_project, update_project and publish_project.
TEXT)]
#[IsReadOnly]
class ListProjects extends Tool
{
    public function handle(Request $request): Response|ResponseFactory
    {
        $projects = Projects::query()->get()
            ->sortByDesc(fn ($entry) => (string) $entry->get('year'))
            ->values()
            ->map(fn ($entry) => Projects::summary($entry))
            ->all();

        $clients = Taxonomy::findOrFail(Projects::TAXONOMY)
            ->queryTerms()
            ->get()
            ->map(fn ($term) => $term->title())
            ->sort()
            ->values()
            ->all();

        return Response::structured([
            'count' => count($projects),
            'projects' => $projects,
            'known_clients_and_agencies' => $clients,
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [];
    }
}
