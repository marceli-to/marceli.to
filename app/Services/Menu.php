<?php
namespace App\Services;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;

class Menu
{
  // Menu item active class
  protected $active = 'is-active';

  public function __construct()
  {
  }

  public function boot()
  {
    $data = [];
    View::share('menuItems', $data);
  }
}