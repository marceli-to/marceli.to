<?php

namespace App\Helpers;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class ImageHelper
{
  static function thumbnail($image, $caption = NULL)
  {
    return '<img src="/img/thumbnail/'.$image->name .'"  width="600" height="600" alt="'. $caption.'">';
  }

  static function openGraphImage($image)
  {
    return "/img/cache/".$image->name."?w=1500&h=1500";
  }
}