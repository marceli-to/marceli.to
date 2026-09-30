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

#[Name('create_project')]
#[Description(<<<'TEXT'
Create a new portfolio project on marceli.to. It is ALWAYS saved as an unpublished draft; publishing is a separate, deliberate step (publish_project).

Required: url (the live website), client, year, description. Optional: agency (the concept/design agency), title (defaults to the domain, e.g. "example.ch", which is how most projects are named), image (asset path from upload_screenshot).

How to use this tool:
1. If the user gives you a screenshot, call upload_screenshot first and pass the returned path as image.
2. Ask the user for every required value you do not know. Never guess or invent a client, agency, year or description.
3. Descriptions are one or two sentences in German, in the style of the existing projects (e.g. "Webseite und Buchungsplattform für die Visualisierungs Akademie in Zürich."). Offer a draft, but let the user decide.
4. For client and agency, reuse the exact spelling of an existing name from list_projects when it is the same company. Unknown names are created as new clients.
5. Show the user all values and get an explicit confirmation BEFORE calling this tool.
TEXT)]
class CreateProject extends Tool
{
    public function handle(Request $request): Response|ResponseFactory
    {
        if (is_int($request->get('year'))) {
            $request->merge(['year' => (string) $request->get('year')]);
        }

        $input = $request->validate(Projects::rules(creating: true), Projects::messages());

        $title = filled($input['title'] ?? null) ? trim($input['title']) : Projects::titleFromUrl($input['url']);

        $entry = Projects::make()
            ->slug(Projects::uniqueSlug($title))
            ->published(false);

        $createdTerms = Projects::fill($entry, [...$input, 'title' => $title]);

        Projects::create($entry);

        Projects::logWrite('create_project', Projects::slugOf($entry), ['id' => $entry->id(), 'new_terms' => $createdTerms]);

        return Response::structured([
            'message' => "Created draft project \"{$title}\". It is not visible on the site until you call publish_project.",
            'new_clients_created' => $createdTerms,
            'project' => Projects::details($entry),
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'url' => $schema->string()->description('Full URL of the live website, e.g. https://example.ch.')->required(),
            'client' => $schema->string()->description('The client, e.g. "Forum Architektur, Winterthur". Reuse an existing name from list_projects when possible.')->required(),
            'year' => $schema->string()->description('Year of launch, e.g. "2025", or a range like "2016 – 2020".')->required(),
            'description' => $schema->string()->description('One or two sentences in German describing the project.')->required(),
            'agency' => $schema->string()->description('Optional. The concept/design agency, e.g. "Bivgrafik GmbH, Zürich".'),
            'title' => $schema->string()->description('Optional. Defaults to the domain of the url, e.g. "example.ch".'),
            'image' => $schema->string()->description('Optional. Asset path returned by upload_screenshot, e.g. "projects/example.ch.png".'),
        ];
    }
}
