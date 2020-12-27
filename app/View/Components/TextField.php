<?php
namespace App\View\Components;
use Illuminate\View\Component;

class TextField extends Component
{

  /**
   * Name
   *
   * @var string
   */
  public $name;

  /**
   * Type
   *
   * @var string
   */
  public $type;

  /**
   * Label
   *
   * @var string
   */
  public $label;

  /**
   * Placeholder
   *
   * @var string
   */
  public $placeholder;

  /**
   * Required
   *
   * @var string
   */
  public $required;

  /**
   * Css
   *
   * @var string
   */
  public $css;

  /**
   * User entered value
   *
   * @var string
   */
  public $userValue;

  /**
   * Create a new component instance.
   *
   * @param $name
   * @param $type
   * @param $label
   * @param $placeholder
   * @param $required
   * @param $css
   * @param $userValue
   * 
   * @return void
   */
  public function __construct($name, $type = 'text', $label, $placeholder = NULL, $required = FALSE, $css = NULL, $userValue = NULL)
  {
    $this->name = $name;
    $this->type = $type;
    $this->label = $label;
    $this->placeholder = $placeholder;
    $this->required = $required;
    $this->css = $css;
    $this->userValue = $userValue;
  }

  /**
   * Get the view / contents that represent the component.
   *
   * @return \Illuminate\View\View|string
   */
  public function render()
  {
    return view('web.components.form.text-field');
  }
}
