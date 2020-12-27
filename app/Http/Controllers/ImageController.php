<?php
namespace App\Http\Controllers;
use Intervention\Image\ImageCacheController;
use Intervention\Image\ImageManager;
use Illuminate\Http\Request;
use Config;

class ImageController extends ImageCacheController
{
  protected $maxWidth;
  protected $maxHeight;
  protected $coords;

  /**
   * Get HTTP response of either original image file or
   * template applied file.
   *
   * @param  string $template
   * @param  string $filename
   * @return Illuminate\Http\Response
   */

  public function getResponse($template, $filename)
  {

    $this->maxWidth  = \Request::get('w');
    $this->maxHeight = \Request::get('h');
    $this->coords    = \Request::get('c');

    switch (strtolower($template)) {
      case 'original':
        return $this->getOriginal($filename);

      case 'download':
        return $this->getDownload($filename);

      default:
        return $this->getImage($template, $filename);
    }
  }

  /**
   * Returns corresponding template object from given template name
   *
   * @param  string $template
   * @return mixed
   */
  protected function getTemplate($template)
  {
    $template = config("imagecache.templates.{$template}");

    switch (true) {
    // closure template found
    case is_callable($template):
      return $template;

    // filter template found
    case class_exists($template):
      return new $template($this->maxWidth, $this->maxHeight, $this->coords);

    default:
      // template not found
      abort(404);
      break;
    }
  }
}
