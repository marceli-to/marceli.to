<?php

namespace Tests\Feature;

use App\Modifiers\ShortName;
use Statamic\Facades\Collection;
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
        $published = $this->publishedProjectFiles(base_path('content'));

        $html = $this->get('/')->assertOk()->getContent();

        $this->assertGreaterThan(1, $published);
        $this->assertSame($published, substr_count($html, '<li data-project'));
    }

    public function test_project_card_shows_its_details(): void
    {
        $project = Collection::find('projects')->queryEntries()
            ->where('published', true)
            ->whereNotNull('client')
            ->whereNotNull('agency')
            ->first();

        $this->assertNotNull($project);

        $this->get('/')
            ->assertOk()
            ->assertSee($project->get('link'), false)
            ->assertSee(explode(',', $project->augmentedValue('client')->value()->title())[0], false)
            ->assertSee(ShortName::shorten($project->augmentedValue('agency')->value()->title()), false)
            ->assertSee($project->get('year'), false);
    }
}
