<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Services\Menu;
use Illuminate\Http\Request;

class BaseController extends Controller
{
  protected $menu;

  /**
   * Constructor
   * 
   */

  public function __construct()
  {

  }
}
