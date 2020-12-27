<?php
namespace App\Stores;
use Illuminate\Http\Request;

class Store
{
  protected $key = 'key';

  /**
   * Set the current store
   * 
   * @param Array $data
   * @return Array $store
   */

  public function set($data)
  { 
    session([$this->key => $data]);
  }

  /**
   * Get the current store
   * 
   * @return Array $store
   */
  public function get()
  {
    if (session()->has($this->key))
    {
      $store = session($this->key);
      return $store;
    }
    return [];
  }

  /**
   * Clear the store
   */
  public function clear()
  {
    session()->forget($this->key);
  }

  /**
   * Set an attribute
   * 
   * @param String $field
   * @param String $value
   * @return Array $store
   */

  public function setAttribute($field, $value)
  { 
    $store = session()->has($this->key) ? session($this->key) : [];
    $store[$field] = $value;
    session([$this->key => $store]);
    return $store;
  }

  /**
   * Get an attribute specified by key
   * 
   * @param String $key
   * @return Mixed $value
   */

  public function getAttribute($key)
  {
    $store = session($this->key);
    if (array_key_exists($key, $store))
    {
      return $store[$key];
    }
    return NULL;
  }
}