<?php

namespace Tests\Feature\Mcp;

use App\Mcp\Servers\PortfolioServer;
use App\Mcp\Tools\GetProject;
use App\Mcp\Tools\ListProjects;
use Statamic\Facades\Collection;

class ReadToolsTest extends McpTestCase
{
    public function test_list_projects_returns_every_project_and_known_clients(): void
    {
        $count = count(glob($this->sandbox.'/content/collections/projects/*/*.md'));
        $this->assertGreaterThan(1, $count);

        PortfolioServer::actingAs($this->superUser())
            ->tool(ListProjects::class)
            ->assertOk()
            ->assertHasNoErrors()
            ->assertStructuredContent(function ($json) use ($count) {
                $json->where('count', $count)
                    ->has('projects', $count, fn ($project) => $project
                        ->hasAll(['id', 'slug', 'title', 'client', 'year', 'url', 'published']))
                    ->where('known_clients_and_agencies', fn ($names) => collect($names)->contains('Bivgrafik GmbH, Zürich'));
            });
    }

    public function test_get_project_returns_full_details(): void
    {
        PortfolioServer::actingAs($this->superUser())
            ->tool(GetProject::class, ['slug' => 'strutch'])
            ->assertOk()
            ->assertHasNoErrors()
            ->assertSee([
                'strut.ch',
                'https://strut.ch',
                'Strut Architekten AG, Winterthur',
                'Bivgrafik GmbH, Zürich',
                'projects/strut.jpg',
                '2019',
            ]);
    }

    public function test_get_project_reports_an_unknown_slug(): void
    {
        PortfolioServer::actingAs($this->superUser())
            ->tool(GetProject::class, ['slug' => 'does-not-exist'])
            ->assertHasErrors(['No project with slug "does-not-exist" exists.']);
    }

    public function test_get_project_asks_for_an_id_when_the_slug_is_ambiguous(): void
    {
        $ids = Collection::find('projects')->queryEntries()->get()->filter(fn ($e) => $e->get('slug') === 'mobilechargech')->map->id()->values();
        $this->assertCount(2, $ids);

        PortfolioServer::actingAs($this->superUser())
            ->tool(GetProject::class, ['slug' => 'mobilechargech'])
            ->assertHasErrors(['Several projects share the slug "mobilechargech"', $ids[0], $ids[1]]);

        PortfolioServer::actingAs($this->superUser())
            ->tool(GetProject::class, ['id' => $ids[0]])
            ->assertOk()
            ->assertHasNoErrors()
            ->assertSee($ids[0]);
    }

    public function test_get_project_requires_a_slug_or_id(): void
    {
        PortfolioServer::actingAs($this->superUser())
            ->tool(GetProject::class, [])
            ->assertHasErrors(['slug']);
    }
}
