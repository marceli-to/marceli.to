<?php

namespace Tests\Feature;

use Statamic\Facades\Entry;
use Statamic\Facades\User;
use Tests\TestCase;

class ControlPanelTest extends TestCase
{
    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/cp')->assertRedirect();
    }

    public function test_super_user_can_open_the_projects_listing(): void
    {
        $this->actingAs($this->superUser())
            ->get('/cp/collections/projects')
            ->assertOk();
    }

    public function test_super_user_can_open_a_project_for_editing(): void
    {
        $project = Entry::query()->where('collection', 'projects')->first();

        $this->actingAs($this->superUser())
            ->get($project->editUrl())
            ->assertOk()
            ->assertSee($project->get('title'), false);
    }

    private function superUser()
    {
        return User::make()->id('smoke-test-user')->email('smoke-test@example.com')->makeSuper();
    }
}
