<?php

namespace Tests\Feature\Mcp;

use App\Mcp\Servers\PortfolioServer;
use App\Mcp\Tools\UploadScreenshot;
use Illuminate\Support\Facades\Http;

class UploadScreenshotTest extends McpTestCase
{
    private function upload(array $arguments)
    {
        return PortfolioServer::actingAs($this->superUser())->tool(UploadScreenshot::class, $arguments);
    }

    private function base64(string $binary, string $mime = 'image/png', string $filename = 'shot.png'): array
    {
        return [
            'website_url' => 'https://www.beispiel.ch/start',
            'data' => base64_encode($binary),
            'mime_type' => $mime,
            'filename' => $filename,
        ];
    }

    private function stored(string $path): array
    {
        return getimagesize($this->sandbox.'/assets/'.$path);
    }

    public function test_it_stores_a_base64_png_named_after_the_domain(): void
    {
        $this->upload($this->base64($this->image(1600, 1067)))
            ->assertOk()
            ->assertHasNoErrors()
            ->assertSee(['"image":"projects/beispiel.ch.png"', '"width":1600', '"height":1067']);

        [$width, $height, $type] = $this->stored('projects/beispiel.ch.png');
        $this->assertSame([1600, 1067, IMAGETYPE_PNG], [$width, $height, $type]);
        $this->assertStringContainsString('upload_screenshot', $this->mcpLog());
    }

    public function test_it_crops_full_page_screenshots_to_3_2_and_scales_down(): void
    {
        $this->upload($this->base64($this->image(3000, 9000)))->assertHasNoErrors();

        [$width, $height] = $this->stored('projects/beispiel.ch.png');
        $this->assertSame([2400, 1600], [$width, $height]);
    }

    public function test_it_crops_wide_screenshots_to_3_2(): void
    {
        $this->upload($this->base64($this->image(2000, 1000)))->assertHasNoErrors();

        [$width, $height] = $this->stored('projects/beispiel.ch.png');
        $this->assertSame([1500, 1000], [$width, $height]);
    }

    public function test_it_saves_jpeg_and_webp_as_jpeg(): void
    {
        $this->upload($this->base64($this->image(1600, 1067, 'jpeg'), 'image/jpeg', 'shot.jpg'))
            ->assertHasNoErrors()
            ->assertSee('projects/beispiel.ch.jpg');

        $this->upload($this->base64($this->image(1600, 1067, 'webp'), 'image/webp', 'shot.webp'))
            ->assertHasNoErrors()
            ->assertSee('projects/beispiel.ch-2.jpg');

        $this->assertSame(IMAGETYPE_JPEG, $this->stored('projects/beispiel.ch-2.jpg')[2]);
    }

    public function test_it_never_overwrites_an_existing_screenshot(): void
    {
        $this->upload($this->base64($this->image(1600, 1067)))->assertSee('projects/beispiel.ch.png');
        $this->upload($this->base64($this->image(1600, 1067)))->assertSee('projects/beispiel.ch-2.png');
    }

    public function test_it_accepts_a_data_uri(): void
    {
        $arguments = $this->base64($this->image(1600, 1067));
        $arguments['data'] = 'data:image/png;base64,'.$arguments['data'];

        $this->upload($arguments)->assertHasNoErrors();
    }

    public function test_it_downloads_from_an_image_url(): void
    {
        Http::fake(['images.example.com/*' => Http::response($this->image(1600, 1067), 200, ['Content-Type' => 'image/png'])]);

        $this->upload(['website_url' => 'https://beispiel.ch', 'image_url' => 'https://images.example.com/shot.png'])
            ->assertHasNoErrors()
            ->assertSee('projects/beispiel.ch.png');
    }

    public function test_it_rejects_image_urls_on_private_networks(): void
    {
        Http::fake();

        $this->upload(['website_url' => 'https://beispiel.ch', 'image_url' => 'http://127.0.0.1/shot.png'])
            ->assertHasErrors(['The image URL must point to a public server.']);

        $this->upload(['website_url' => 'https://beispiel.ch', 'image_url' => 'http://192.168.1.10/shot.png'])
            ->assertHasErrors(['The image URL must point to a public server.']);

        Http::assertNothingSent();
    }

    public function test_it_reports_failed_downloads_and_redirects(): void
    {
        Http::fake([
            'images.example.com/missing.png' => Http::response('', 404),
            'images.example.com/moved.png' => Http::response('', 302, ['Location' => 'https://cdn.example.com/shot.png']),
        ]);

        $this->upload(['website_url' => 'https://beispiel.ch', 'image_url' => 'https://images.example.com/missing.png'])
            ->assertHasErrors(['The image URL returned HTTP 404.']);

        $this->upload(['website_url' => 'https://beispiel.ch', 'image_url' => 'https://images.example.com/moved.png'])
            ->assertHasErrors(['redirects to https://cdn.example.com/shot.png']);
    }

    public function test_it_validates_the_input(): void
    {
        $this->upload(['website_url' => 'https://beispiel.ch'])
            ->assertHasErrors(['Send the screenshot as base64 "data"']);

        $this->upload([...$this->base64($this->image(1600, 1067)), 'image_url' => 'https://images.example.com/shot.png'])
            ->assertHasErrors(['Send either "data" or "image_url", not both.']);

        $this->upload([...$this->base64($this->image(1600, 1067)), 'website_url' => 'beispiel'])
            ->assertHasErrors(['website url']);

        $this->upload($this->base64($this->image(1600, 1067), 'image/gif', 'shot.gif'))
            ->assertHasErrors(['The mime_type must be image/jpeg, image/png or image/webp.']);
    }

    public function test_it_rejects_unusable_images(): void
    {
        $this->upload([...$this->base64('x'), 'data' => 'not base64!'])
            ->assertHasErrors(['The data is not valid base64.']);

        $this->upload($this->base64($this->image(1600, 1067, 'gif')))
            ->assertHasErrors(['The file is image/gif. Only JPEG, PNG and WebP screenshots are accepted.']);

        $this->upload($this->base64($this->image(800, 600)))
            ->assertHasErrors(['The screenshot is 800px wide. It must be at least 1200px wide']);

        $this->upload($this->base64(str_repeat('a', 11 * 1024 * 1024)))
            ->assertHasErrors(['The limit is 10 MB.']);

        $this->assertSame([], glob($this->sandbox.'/assets/projects/*'));
    }
}
