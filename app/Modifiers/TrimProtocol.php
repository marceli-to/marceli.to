<?php

namespace App\Modifiers;

use Statamic\Modifiers\Modifier;

class TrimProtocol extends Modifier
{
    /**
     * Modify a value.
     *
     * @param mixed  $value    The value to be modified
     * @param array  $params   Any parameters used in the modifier
     * @param array  $context  Contextual values
     * @return mixed
     */
    public function index($value, $params, $context)
    {
      $value = str_replace(['http://', 'https://'], '', $value);
      return $value;
    }
}
