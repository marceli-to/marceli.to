<?php
namespace App\View\Components;
use Illuminate\View\Component;

class Image extends Component
{
  /**
   * Image
   *
   * @var object
   */
  public $image;

  /**
   * Template
   *
   * @var string
   */
  public $template;

  /**
   * Caption
   *
   * @var boolean
   */
  public $hasCaption;

  /**
   * Lazy loading
   *
   * @var boolean
   */
  public $hasLazy;

  /**
   * Additional css class
   *
   * @var string
   */
  public $cssClass;

  /**
   * Create a new component instance.
   *
   * @return void
   */
  public function __construct($image = NULL, $template = 'large', $hasCaption = NULL, $hasLazy = FALSE, $cssClass = NULL)
  {
    $this->image       = $image;
    $this->template    = $template;
    $this->hasCaption  = $hasCaption;
    $this->hasLazy     = $hasLazy;
    $this->cssClass    = $cssClass;
  }

  /**
   * Get the view / contents that represent the component.
   *
   * @return \Illuminate\View\View|string
   */
  public function render()
  {
    return view('web.components.content.image');
  }
}
