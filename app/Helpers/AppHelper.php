<?php
namespace App\Helpers;
use App\Models\CourseEventStudent;
use App\Models\SymposiumSubscriber;
use Illuminate\Support\Str;

class AppHelper
{
  public static function slug($str = NULL)
  {
    $slug = '';
    $slug = Str::slug(AppHelper::transliterate($str), '-');
    return $slug;
  }

  public static function nl2p($string = NULL)
  {
    $string = nl2br($string, false);
    return '<p>' . preg_replace('#(<br>[\r\n\s]+){2}#', '</p><p>', $string) . '</p>';
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

  public static function number($amount)
  {
    return number_format(round($amount * 20) / 20, 2, '.', '');
  }

}