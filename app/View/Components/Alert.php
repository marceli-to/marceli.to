<?php
namespace App\View\Components;
use Illuminate\View\Component;

class Alert extends Component
{

  /**
   * Type
   *
   * @var string
   */
  public $type;

  /**
   * Message
   *
   * @var string
   */
  public $message;

  /**
   * Create a new component instance.
   *
   * @return void
   */
  public function __construct($type = NULL, $message = NULL)
  {
    $this->type = $type;
    $this->message = $message;
  }

  /**
   * Get the view / contents that represent the component.
   *
   * @return \Illuminate\View\View|string
   */
  public function render()
  {
    return view('web.components.content.alert');
  }
}
