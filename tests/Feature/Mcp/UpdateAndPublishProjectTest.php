<?php

namespace Tests\Feature\Mcp;

use App\Mcp\Servers\PortfolioServer;
use App\Mcp\Tools\CreateProject;
use App\Mcp\Tools\PublishProject;
use App\Mcp\Tools\UpdateProject;
use App\Mcp\Tools\UploadScreenshot;
use Statamic\Facades\Entry;

class UpdateAndPublishProjectTest extends McpTestCase
{
    private function createDraft(array $overrides = []): void
    {
        PortfolioServer::actingAs($this->superUser())
            ->tool(CreateProject::class, [
                'url' => 'https://entwurf.ch',
                'client' => 'Ferratec AG, Rudolfstetten',
                'year' => '2025',
                'description' => 'Webseite für einen Entwurf in Zürich.',
                ...$overrides,
            ])
            ->assertHasNoErrors();
    }

    private function uploadScreenshot(): void
    {
        PortfolioServer::actingAs($this->superUser())
            ->tool(UploadScreenshot::class, [
                'website_url' => 'https://entwurf.ch',
                'data' => base64_encode($this->image(1600, 1067)),
                'mime_type' => 'image/png',
                'filename' => 'entwurf.png',
            ])
            ->assertHasNoErrors();
    }

    public function test_update_changes_only_the_given_fields(): void
    {
        PortfolioServer::actingAs($this->superUser())
            ->tool(UpdateProject::class, ['slug' => 'strutch', 'description' => 'Neue Beschreibung für Strut Architekten.'])
            ->assertOk()
            ->assertHasNoErrors()
            ->assertSee(['Updated description', 'the change is live']);

        $entry = Entry::find('05020ddc-dca8-43a9-800d-90820acf58f8');

        $this->assertSame('Neue Beschreibung für Strut Architekten.', $entry->get('description'));
        $this->assertSame('strut.ch', $entry->get('title'));
        $this->assertSame('strut-architekten-ag-winterthur', $entry->get('client'));
        $this->assertSame('2019', $entry->get('year'));
        $this->assertStringContainsString('update_project strutch', $this->mcpLog());
    }

    public function test_update_can_change_client_and_remove_agency(): void
    {
        PortfolioServer::actingAs($this->superUser())
            ->tool(UpdateProject::class, ['slug' => 'strutch', 'client' => 'Ferratec AG, Rudolfstetten', 'agency' => ''])
            ->assertHasNoErrors();

        $entry = Entry::find('05020ddc-dca8-43a9-800d-90820acf58f8');

        $this->assertSame('ferratec-ag-rudolfstetten', $entry->get('client'));
        $this->assertNull($entry->get('agency'));
    }

    public function test_update_rejects_emptying_required_fields(): void
    {
        PortfolioServer::actingAs($this->superUser())
            ->tool(UpdateProject::class, ['slug' => 'strutch', 'client' => ''])
            ->assertHasErrors(['The client field must have a value.']);

        $this->assertSame('strut-architekten-ag-winterthur', Entry::find('05020ddc-dca8-43a9-800d-90820acf58f8')->get('client'));
    }

    public function test_update_requires_at_least_one_field_and_a_known_project(): void
    {
        PortfolioServer::actingAs($this->superUser())
            ->tool(UpdateProject::class, ['slug' => 'strutch'])
            ->assertHasErrors(['Pass at least one field to change']);

        PortfolioServer::actingAs($this->superUser())
            ->tool(UpdateProject::class, ['slug' => 'nope', 'year' => '2020'])
            ->assertHasErrors(['No project with slug "nope" exists.']);

        PortfolioServer::actingAs($this->superUser())
            ->tool(UpdateProject::class, ['slug' => 'strutch', 'year' => '20'])
            ->assertHasErrors(['four-digit year']);

        $this->assertSame('', $this->mcpLog());
    }

    public function test_publish_requires_a_screenshot(): void
    {
        $this->createDraft();

        PortfolioServer::actingAs($this->superUser())
            ->tool(PublishProject::class, ['slug' => 'entwurfch'])
            ->assertHasErrors(['Cannot publish yet, missing: image.']);

        $this->assertFalse(Entry::query()->where('collection', 'projects')->where('slug', 'entwurfch')->first()->published());
    }

    public function test_publish_publishes_a_complete_draft(): void
    {
        $this->uploadScreenshot();
        $this->createDraft(['image' => 'projects/entwurf.ch.png']);

        PortfolioServer::actingAs($this->superUser())
            ->tool(PublishProject::class, ['slug' => 'entwurfch'])
            ->assertOk()
            ->assertHasNoErrors()
            ->assertSee(['Published', '"published":true']);

        $this->assertTrue(Entry::query()->where('collection', 'projects')->where('slug', 'entwurfch')->first()->published());
        $this->assertStringContainsString('publish_project entwurfch', $this->mcpLog());

        PortfolioServer::actingAs($this->superUser())
            ->tool(PublishProject::class, ['slug' => 'entwurfch'])
            ->assertHasNoErrors()
            ->assertSee('is already published');
    }

    public function test_publish_reports_an_unknown_project(): void
    {
        PortfolioServer::actingAs($this->superUser())
            ->tool(PublishProject::class, ['slug' => 'nope'])
            ->assertHasErrors(['No project with slug "nope" exists.']);
    }
}
