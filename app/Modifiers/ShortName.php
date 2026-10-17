<?php

namespace App\Modifiers;

use Statamic\Modifiers\Modifier;

class ShortName extends Modifier
{
  /**
   * Shorten a client or studio name: drop the location and a trailing legal form.
   * "Bivgrafik GmbH, Zürich" becomes "Bivgrafik"; "WBG AG – Visuelle Kommunikation, Zürich"
   * keeps its tagline and becomes "WBG AG – Visuelle Kommunikation".
   *
   * @param mixed  $value    The value to be modified
   * @param array  $params   Any parameters used in the modifier
   * @param array  $context  Contextual values
   * @return mixed
   */
  public function index($value, $params, $context)
  {
    return self::shorten((string) $value);
  }

  public static function shorten(string $name): string
  {
    $name = explode(',', $name)[0];
    return trim(preg_replace('/\s+(AG|GmbH)$/', '', $name));
  }
}
