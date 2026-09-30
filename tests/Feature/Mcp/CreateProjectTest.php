<?php

namespace Tests\Feature\Mcp;

use App\Mcp\Servers\PortfolioServer;
use App\Mcp\Tools\CreateProject;
use App\Mcp\Tools\UploadScreenshot;
use Statamic\Facades\Collection;
use Statamic\Facades\Taxonomy;

class CreateProjectTest extends McpTestCase
{
    private function valid(array $overrides = []): array
    {
        return [
            'url' => 'https://www.neues-projekt.ch/',
            'client' => 'Bivgrafik GmbH, Zürich',
            'year' => '2025',
            'description' => 'Webseite für ein neues Projekt in Zürich.',
            ...$overrides,
        ];
    }

    private function project(string $slug)
    {
        return Collection::find('projects')->queryEntries()->get()->first(fn ($e) => $e->slug() === $slug);
    }

    public function test_it_creates_an_unpublished_draft(): void
    {
        $projectsBefore = count($this->sandboxEntryFiles());
        $termsBefore = Taxonomy::find('clients')->queryTerms()->count();

        PortfolioServer::actingAs($this->superUser())
            ->tool(CreateProject::class, $this->valid(['agency' => 'Neue Agentur GmbH, Bern']))
            ->assertOk()
            ->assertHasNoErrors()
            ->assertSee(['Created draft project', 'neues-projekt.ch', '"published":false', 'Neue Agentur GmbH, Bern']);

        $entry = $this->project('neues-projektch');

        $this->assertNotNull($entry);
        $this->assertFalse($entry->published());
        $this->assertSame('neues-projekt.ch', $entry->get('title'));
        $this->assertSame('https://www.neues-projekt.ch/', $entry->get('link'));
        $this->assertSame('2025', $entry->get('year'));
        $this->assertSame('bivgrafik-gmbh-zurich', $entry->get('client'), 'Existing clients are reused, not duplicated.');
        $this->assertSame('neue-agentur-gmbh-bern', $entry->get('agency'));
        $this->assertSame('project', $entry->blueprint()->handle());

        $this->assertCount($projectsBefore + 1, $this->sandboxEntryFiles());
        $this->assertSame($termsBefore + 1, Taxonomy::find('clients')->queryTerms()->count());

        $tree = Collection::find('projects')->structure()->in('de')->tree();
        $this->assertSame($entry->id(), end($tree)['entry'], 'New projects are appended to the collection tree.');
    }

    public function test_it_matches_existing_clients_case_insensitively(): void
    {
        PortfolioServer::actingAs($this->superUser())
            ->tool(CreateProject::class, $this->valid(['client' => '  bivgrafik gmbh, zürich ']))
            ->assertHasNoErrors()
            ->assertSee('"new_clients_created":[]');
    }

    public function test_it_uses_a_given_title_and_keeps_slugs_unique(): void
    {
        PortfolioServer::actingAs($this->superUser())
            ->tool(CreateProject::class, $this->valid(['title' => 'Grüne Wiese']))
            ->assertHasNoErrors();

        PortfolioServer::actingAs($this->superUser())
            ->tool(CreateProject::class, $this->valid(['title' => 'Grüne Wiese']))
            ->assertHasNoErrors();

        $this->assertNotNull($this->project('gruene-wiese'));
        $this->assertNotNull($this->project('gruene-wiese-2'));
    }

    public function test_it_normalises_year_ranges(): void
    {
        PortfolioServer::actingAs($this->superUser())
            ->tool(CreateProject::class, $this->valid(['year' => '2019-2021']))
            ->assertHasNoErrors();

        $this->assertSame('2019 – 2021', $this->project('neues-projektch')->get('year'));
    }

    public function test_it_accepts_an_uploaded_screenshot(): void
    {
        PortfolioServer::actingAs($this->superUser())
            ->tool(UploadScreenshot::class, [
                'website_url' => 'https://neues-projekt.ch',
                'data' => base64_encode($this->image(1600, 1067)),
                'mime_type' => 'image/png',
                'filename' => 'shot.png',
            ])
            ->assertHasNoErrors();

        PortfolioServer::actingAs($this->superUser())
            ->tool(CreateProject::class, $this->valid(['image' => 'projects/neues-projekt.ch.png']))
            ->assertHasNoErrors();

        $this->assertSame('projects/neues-projekt.ch.png', $this->project('neues-projektch')->get('image'));
    }

    public function test_it_requires_url_client_year_and_description(): void
    {
        PortfolioServer::actingAs($this->superUser())
            ->tool(CreateProject::class, [])
            ->assertHasErrors([
                'The url field is required.',
                'The client field is required.',
                'The year field is required.',
                'The description field is required.',
            ]);

        $this->assertNull($this->project('neues-projektch'));
    }

    public function test_it_rejects_invalid_values(): void
    {
        PortfolioServer::actingAs($this->superUser())
            ->tool(CreateProject::class, $this->valid(['url' => 'neues-projekt.ch']))
            ->assertHasErrors(['The url must be a full http(s) URL']);

        PortfolioServer::actingAs($this->superUser())
            ->tool(CreateProject::class, $this->valid(['year' => '1987']))
            ->assertHasErrors(['The year must be between 1995 and']);

        PortfolioServer::actingAs($this->superUser())
            ->tool(CreateProject::class, $this->valid(['year' => 'last year']))
            ->assertHasErrors(['four-digit year']);

        PortfolioServer::actingAs($this->superUser())
            ->tool(CreateProject::class, $this->valid(['year' => '2021 – 2019']))
            ->assertHasErrors(['from the earlier to the later year']);

        PortfolioServer::actingAs($this->superUser())
            ->tool(CreateProject::class, $this->valid(['description' => 'Kurz.']))
            ->assertHasErrors(['The description is too short.']);

        PortfolioServer::actingAs($this->superUser())
            ->tool(CreateProject::class, $this->valid(['image' => 'projects/missing.png']))
            ->assertHasErrors(['The image must be an asset path returned by upload_screenshot']);

        $this->assertNull($this->project('neues-projektch'));
    }

    public function test_it_logs_the_write(): void
    {
        PortfolioServer::actingAs($this->superUser())
            ->tool(CreateProject::class, $this->valid())
            ->assertHasNoErrors();

        $this->assertStringContainsString('create_project neues-projektch', $this->mcpLog());
        $this->assertStringContainsString('"user":"mcp-test@example.com"', $this->mcpLog());
        $this->assertStringContainsString('"timestamp":', $this->mcpLog());
    }
}
