<?php

use App\Http\Controllers\ScreenshotUploadController;
use App\Http\Middleware\AuthenticateMcpToken;
use App\Mcp\Servers\PortfolioServer;
use Illuminate\Support\Facades\Route;
use Laravel\Mcp\Facades\Mcp;

Mcp::web('/mcp', PortfolioServer::class)
    ->middleware(['throttle:mcp', AuthenticateMcpToken::class]);

Route::post('/mcp/screenshots', ScreenshotUploadController::class)
    ->middleware(['throttle:mcp', 'signed:relative'])
    ->name('mcp.screenshots.upload');
