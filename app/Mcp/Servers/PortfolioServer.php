<?php

namespace App\Mcp\Servers;

use App\Mcp\Tools\CreateProject;
use App\Mcp\Tools\GetProject;
use App\Mcp\Tools\ListProjects;
use App\Mcp\Tools\PublishProject;
use App\Mcp\Tools\UpdateProject;
use App\Mcp\Tools\UploadScreenshot;
use Laravel\Mcp\Server;
use Laravel\Mcp\Server\Attributes\Instructions;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Version;

#[Name('marceli.to Portfolio')]
#[Version('1.0.0')]
#[Instructions(<<<'TEXT'
Manage the portfolio projects shown on marceli.to (Marcel Stadelmann's web development portfolio). Each project is one website he built: a screenshot, the website URL, client, optional concept/design agency, year and a short German description.

Typical flow: the user drops in a screenshot -> upload_screenshot -> ask for any missing details -> confirm all values -> create_project (saved as a draft) -> publish_project only when the user asks.
Always confirm values with the user before writing. Never invent clients, agencies, years or descriptions. Deleting projects is not possible here; that is done in the Statamic Control Panel.
TEXT)]
class PortfolioServer extends Server
{
    protected array $tools = [
        ListProjects::class,
        GetProject::class,
        CreateProject::class,
        UpdateProject::class,
        UploadScreenshot::class,
        PublishProject::class,
    ];

    protected array $resources = [
        //
    ];

    protected array $prompts = [
        //
    ];

    /**
     * Tool messages are in English; the site locale is "de" without German validation strings.
     */
    protected function boot(): void
    {
        app()->setLocale('en');
    }
}
