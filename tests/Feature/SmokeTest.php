<?php

namespace Tests\Feature;

use Statamic\Facades\Entry;
use Tests\TestCase;

class SmokeTest extends TestCase
{
    public function test_home_page_renders(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('<main role="main"', false);
    }

    public function test_home_page_lists_every_published_project(): void
    {
        $published = Entry::query()
            ->where('collection', 'projects')
            ->where('published', true)
            ->count();

        $html = $this->get('/')->assertOk()->getContent();

        $this->assertGreaterThan(0, $published);
        $this->assertSame($published, substr_count($html, '<article'));
    }

    public function test_project_card_shows_its_details(): void
    {
        $project = Entry::query()
            ->where('collection', 'projects')
            ->where('published', true)
            ->whereNotNull('client')
            ->whereNotNull('agency')
            ->first();

        $this->assertNotNull($project);

        $this->get('/')
            ->assertOk()
            ->assertSee($project->get('link'), false)
            ->assertSee($project->augmentedValue('client')->value()->title(), false)
            ->assertSee($project->augmentedValue('agency')->value()->title(), false)
            ->assertSee($project->get('year'), false);
    }
}
