<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class McpToken extends Command
{
    protected $signature = 'mcp:token';

    protected $description = 'Generate a bearer token for the MCP endpoint and print the hash for .env';

    public function handle(): int
    {
        $token = 'mcp_'.Str::random(48);

        $this->line('Add this to .env (only the hash is stored on the server):');
        $this->newLine();
        $this->line('MCP_TOKEN_HASH='.hash('sha256', $token));
        $this->newLine();
        $this->line('Bearer token for your MCP client (shown once, keep it secret):');
        $this->newLine();
        $this->line($token);

        return self::SUCCESS;
    }
}
