<?php
namespace App\Filters\Image\Template;
use Intervention\Image\Image;
use Intervention\Image\Filters\FilterInterface;

class Large implements FilterInterface
{
  /**
   * Maximum width for large landscape images
   */    
  protected $max_width = 1600;    

  /**
   * Maximum height for large portrait images
   */    
  protected $max_height = 1200;
  
  public function applyFilter(Image $image)
  {
    // Get width and height
    $width  = $image->getWidth();
    $height = $image->getHeight();

    // Resize landscape image
    if ($width > $height)
    {
      return $image->resize($this->max_width, null, function ($constraint) {
        $constraint->aspectRatio();
        $constraint->upsize();
      });
    }
    else
    {
      return $image->resize(null, $this->max_height, function ($constraint) {
        $constraint->aspectRatio();
        $constraint->upsize();
      });
    }
    return $image;
  }
}