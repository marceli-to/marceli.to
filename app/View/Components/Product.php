<?php
namespace App\View\Components;
use Illuminate\View\Component;

class Product extends Component
{
  /**
   * Image
   *
   * @var string
   */
  public $image;

  /**
   * Logo
   *
   * @var string
   */
  public $logo;

  /**
   * Title
   *
   * @var string
   */
  public $title;

  /**
   * Theme
   *
   * @var string
   */
  public $theme;


  public $slug;

  /**
   * Create a new component instance.
   *
   * @return void
   */
  public function __construct($image = NULL, $logo = NULL, $title = NULL, $theme = NULL)
  {
    $this->image = $image;
    $this->logo  = $logo;
    $this->title = $title;
    $this->theme = $theme;
    $this->slug  = \Str::slug($title);
  }

  /**
   * Get the view / contents that represent the component.
   *
   * @return \Illuminate\View\View|string
   */
  public function render()
  {
    return view('web.components.content.product');
  }
}
