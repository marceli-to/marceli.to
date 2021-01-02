<?php
namespace App\View\Components;
use Illuminate\View\Component;

class Card extends Component
{

  /**
   * Uri
   *
   * @var string
   */
  public $uri;

  /**
   * Website
   *
   * @var string
   */
  public $website;

  /**
   * Description
   *
   * @var string
   */
  public $description;

  /**
   * Image
   *
   * @var string
   */
  public $image;

  /**
   * Client
   *
   * @var string
   */
  public $client;

  /**
   * Design
   *
   * @var string
   */
  public $design;

  /**
   * Year
   *
   * @var string
   */
  public $year;

  /**
   * Create a new component instance.
   *
   * @return void
   */
  public function __construct($uri = NULL, $website = NULL, $description = NULL, $image = NULL, $client = NULL, $design = NULL, $year = NULL)
  {
    $this->uri          = $uri;
    $this->website      = $website;
    $this->description  = $description;
    $this->image        = $image;
    $this->client       = $client;
    $this->design       = $design;
    $this->year         = $year;
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
