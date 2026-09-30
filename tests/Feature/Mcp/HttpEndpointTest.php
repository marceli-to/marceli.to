<?php

namespace Tests\Feature\Mcp;

use App\Mcp\Tools\CreateProject;
use Illuminate\Support\Facades\RateLimiter;

class HttpEndpointTest extends McpTestCase
{
    private const TOKEN = 'mcp_test-token';

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.mcp.token_hash' => hash('sha256', self::TOKEN),
            'services.mcp.user_email' => 'm@marceli.to',
        ]);

        RateLimiter::clear('mcp');
    }

    private function rpc(string $method, array $params = [], ?string $token = self::TOKEN)
    {
        $headers = ['Accept' => 'application/json, text/event-stream'];

        if ($token !== null) {
            $headers['Authorization'] = "Bearer {$token}";
        }

        return $this->postJson('/mcp', ['jsonrpc' => '2.0', 'id' => 1, 'method' => $method, 'params' => $params], $headers);
    }

    public function test_requests_without_a_token_are_rejected(): void
    {
        $this->rpc('tools/list', token: null)
            ->assertUnauthorized()
            ->assertHeader('WWW-Authenticate');
    }

    public function test_requests_with_a_wrong_token_are_rejected(): void
    {
        $this->rpc('tools/list', token: 'mcp_wrong')->assertUnauthorized();
    }

    public function test_tool_calls_without_a_token_do_not_write(): void
    {
        $files = count($this->sandboxEntryFiles());

        $this->rpc('tools/call', [
            'name' => 'create_project',
            'arguments' => ['url' => 'https://x.ch', 'client' => 'X', 'year' => '2025', 'description' => 'Eine Beschreibung für X.'],
        ], token: null)->assertUnauthorized();

        $this->assertCount($files, $this->sandboxEntryFiles());
    }

    public function test_it_is_disabled_until_a_token_hash_is_configured(): void
    {
        config(['services.mcp.token_hash' => null]);

        $this->rpc('tools/list')->assertUnauthorized();
    }

    public function test_the_mcp_user_must_be_a_super_user(): void
    {
        config(['services.mcp.user_email' => 'nobody@example.com']);

        $this->rpc('tools/list')->assertForbidden();
    }

    public function test_an_authenticated_client_can_list_the_tools(): void
    {
        $tools = collect($this->rpc('tools/list')->assertOk()->json('result.tools'))->pluck('name')->sort()->values()->all();

        $this->assertSame(['create_project', 'get_project', 'get_screenshot_upload_url', 'list_projects', 'publish_project', 'update_project', 'upload_screenshot'], $tools);
    }

    public function test_an_authenticated_client_can_call_a_tool_and_the_write_is_attributed(): void
    {
        $this->rpc('tools/call', [
            'name' => 'create_project',
            'arguments' => ['url' => 'https://http-test.ch', 'client' => 'Ferratec AG, Rudolfstetten', 'year' => '2025', 'description' => 'Webseite für einen HTTP Test.'],
        ])->assertOk()->assertJsonPath('result.isError', false);

        $this->assertStringContainsString('create_project http-testch', $this->mcpLog());
        $this->assertStringContainsString('"user":"m@marceli.to"', $this->mcpLog());
    }

    public function test_there_is_no_delete_tool(): void
    {
        $descriptions = collect($this->rpc('tools/list')->json('result.tools'))->pluck('name')->implode(' ');

        $this->assertStringNotContainsString('delete', $descriptions);
        $this->assertStringContainsString('ALWAYS saved as an unpublished draft', (new CreateProject)->description());
    }

    public function test_the_endpoint_is_rate_limited(): void
    {
        for ($i = 0; $i < 60; $i++) {
            $this->rpc('tools/list', token: 'mcp_wrong')->assertUnauthorized();
        }

        $this->rpc('tools/list')->assertTooManyRequests();
    }
}
