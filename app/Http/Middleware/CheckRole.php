<?php
namespace App\Http\Middleware;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use Closure;

class CheckRole
{
  /**
   * Handle the incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure  $next
   * @param  string  $role
   * @return mixed
   */
  public function handle($request, Closure $next, $role)
  {
    if (Auth::user()->role !== $role && Auth::user()->role !== 'admin')
    {
      return abort(403);
    }
    return $next($request);
  }
}