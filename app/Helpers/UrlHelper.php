<?php
namespace App\Helpers;
use Illuminate\Support\Str;

class UrlHelper
{
  public static function slug($str = NULL)
  {
    $slug = '';
    $slug = Str::slug(UrlHelper::transliterate($str), '-');
    return $slug;
  }

  public static function transliterate($string = NULL)
  {
    $search = array(
      'ä', 'ö', 'ü', 'é', 'è', 'â', 'à', 'ç',
    );

    $replace = array(
      'ae', 'oe', 'ue', 'e', 'e', 'a', 'a', 'c',
    );
    return (string) str_replace($search, $replace, mb_strtolower($string, 'UTF-8'));
  }

}