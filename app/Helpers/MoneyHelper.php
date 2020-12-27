<?php
namespace App\Helpers;

class MoneyHelper
{
  public static function round($amount)
  {
    return number_format(round($amount * 20) / 20, 2, '.', '');
  }
}