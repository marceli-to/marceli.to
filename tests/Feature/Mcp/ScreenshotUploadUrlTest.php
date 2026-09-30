<?php

namespace Tests\Feature\Mcp;

use App\Mcp\Servers\PortfolioServer;
use App\Mcp\Tools\GetScreenshotUploadUrl;
use Illuminate\Support\Facades\RateLimiter;

class ScreenshotUploadUrlTest extends McpTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        RateLimiter::clear('mcp');
    }

    private function uploadUrl(string $websiteUrl = 'https://www.beispiel.ch/start'): string
    {
        $response = PortfolioServer::actingAs($this->superUser())
            ->tool(GetScreenshotUploadUrl::class, ['website_url' => $websiteUrl])
            ->assertHasNoErrors();

        $url = null;

        $response->assertStructuredContent(function ($json) use (&$url) {
            $url = $json->toArray()['upload_url'];
            $json->etc();
        });

        return $url;
    }

    private function send(string $url, string $body)
    {
        return $this->call('POST', $url, [], [], [], ['CONTENT_TYPE' => 'application/octet-stream'], $body);
    }

    public function test_a_local_file_can_be_uploaded_to_the_signed_url(): void
    {
        $this->send($this->uploadUrl(), $this->image(3000, 9000))
            ->assertOk()
            ->assertJson(['image' => 'projects/beispiel.ch.png', 'width' => 2400, 'height' => 1600]);

        [$width, $height, $type] = getimagesize($this->sandbox.'/assets/projects/beispiel.ch.png');
        $this->assertSame([2400, 1600, IMAGETYPE_PNG], [$width, $height, $type]);
        $this->assertStringContainsString('upload_screenshot', $this->mcpLog());
    }

    public function test_the_url_works_only_once(): void
    {
        $url = $this->uploadUrl();

        $this->send($url, $this->image(1600, 1067))->assertOk();
        $this->send($url, $this->image(1600, 1067))->assertStatus(410);

        $this->assertCount(1, glob($this->sandbox.'/assets/projects/*'));
    }

    public function test_a_rejected_file_can_be_retried_with_the_same_url(): void
    {
        $url = $this->uploadUrl();

        $this->send($url, $this->image(800, 600))
            ->assertStatus(422)
            ->assertJsonPath('error', fn ($error) => str_contains($error, 'at least 1200px wide'));

        $this->send($url, '')->assertStatus(422)->assertJsonPath('error', fn ($error) => str_contains($error, 'empty'));

        $this->send($url, $this->image(1600, 1067))->assertOk();
    }

    public function test_unsigned_tampered_or_expired_urls_are_rejected(): void
    {
        $url = $this->uploadUrl();

        $this->send('/mcp/screenshots?website_url=https://beispiel.ch', $this->image(1600, 1067))->assertForbidden();
        $this->send(str_replace('beispiel.ch', 'evil.ch', $url), $this->image(1600, 1067))->assertForbidden();

        $this->travel(GetScreenshotUploadUrl::TTL_MINUTES + 1)->minutes();
        $this->send($url, $this->image(1600, 1067))->assertForbidden();

        $this->assertSame([], glob($this->sandbox.'/assets/projects/*'));
    }
}
