<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends BaseController
{
  /*
  |--------------------------------------------------------------------------
  | Login Controller
  |--------------------------------------------------------------------------
  |
  | This controller handles authenticating users for the application and
  | redirecting them to your home screen. The controller uses a trait
  | to conveniently provide its functionality to your applications.
  |
  */

  use AuthenticatesUsers;

  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct()
  {
    parent::__construct();
    $this->middleware('guest')->except('logout');
  }

  /**
   * Redirect the user based on his role
   * 
   * @return string
   */
  public function redirectTo()
  {
    if (auth()->user()->isAdmin())
    {
      return RouteServiceProvider::DASHBOARD_ADMINSTRATION;
    }
    else
    {
      return RouteServiceProvider::HOME;
    }
  }
}
