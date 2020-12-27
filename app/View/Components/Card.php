<?php
namespace App\View\Components;
use Illuminate\View\Component;

class Card extends Component
{

  /**
   * Id
   *
   * @var integer
   */
  public $id;

  /**
   * Type
   *
   * @var string
   */
  public $type;

  /**
   * Image
   *
   * @var object
   */
  public $image;

  /**
   * Title
   *
   * @var string
   */
  public $title;

  /**
   * Subtitle
   *
   * @var string
   */
  public $subtitle;

  /**
   * Create a new component instance.
   *
   * @return void
   */
  public function __construct($id = NULL, $type = NULL, $image = NULL, $title = NULL, $subtitle = NULL)
  {
    $this->id       = $id;
    $this->type     = $type;
    $this->image    = $image;
    $this->title    = $title;
    $this->subtitle = $subtitle;
  }

  /**
   * Get the view / contents that represent the component.
   *
   * @return \Illuminate\View\View|string
   */
  public function render()
  {
    return view('web.components.content.card');
  }
}
