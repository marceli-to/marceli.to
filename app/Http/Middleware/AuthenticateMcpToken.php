<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Statamic\Facades\User;
use Symfony\Component\HttpFoundation\Response;

/**
 * Authenticates MCP requests with a static bearer token.
 *
 * Only a SHA-256 hash of the token is stored (MCP_TOKEN_HASH); requests are
 * authenticated as the Statamic user in MCP_USER_EMAIL. Create a token with
 * `php artisan mcp:token`.
 */
class AuthenticateMcpToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $expected = config('services.mcp.token_hash');
        $token = $request->bearerToken();

        if (! $expected || ! $token || ! hash_equals($expected, hash('sha256', $token))) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        $user = User::findByEmail((string) config('services.mcp.user_email'));

        if (! $user || ! $user->isSuper()) {
            return response()->json(['error' => 'The MCP user is not configured.'], 403);
        }

        Auth::setUser($user);

        return $next($request);
    }
}
