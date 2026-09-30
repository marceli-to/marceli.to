<?php

use App\Http\Middleware\AuthenticateMcpToken;
use App\Mcp\Servers\PortfolioServer;
use Laravel\Mcp\Facades\Mcp;

Mcp::web('/mcp', PortfolioServer::class)
    ->middleware(['throttle:mcp', AuthenticateMcpToken::class]);
