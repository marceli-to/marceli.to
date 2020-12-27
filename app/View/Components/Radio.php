<?php
namespace App\View\Components;
use Illuminate\View\Component;

class Radio extends Component
{

  /**
   * Name
   *
   * @var string
   */
  public $name;

  /**
   * Id
   *
   * @var string
   */
  public $id;
 
  /**
   * Label
   *
   * @var string
   */
  public $label;

  /**
   * isChecked
   *
   * @var boolean
   */
  public $checked;

  /**
   * Create a new component instance.
   *
   * @return void
   */
  public function __construct($name, $label, $id, $checked = false)
  {
    $this->name     = $name;
    $this->label    = $label;
    $this->id       = $id;
    $this->checked  = $checked;
  }

  /**
   * Get the view / contents that represent the component.
   *
   * @return \Illuminate\View\View|string
   */
  public function render()
  {
    return view('web.components.form.radio');
  }
}
