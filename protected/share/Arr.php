<?php
class Arr
{
  /**
   * @param mixed $array
   * @param mixed $key
   * @param mixed $default
   * @param mixed $strict_default_check
   *
   * @return mixed
   */
  public static function get($array, $key, $default = null, $strict_default_check = false)
  {
    if( $strict_default_check )
      return !empty($array[$key]) ? $array[$key] : $default;
    else
      return isset($array[$key]) ? $array[$key] : $default;
  }

  /**
   * вырезаем ключ из массива
   *
   * @static
   *
   * @param      $array
   * @param      $key
   * @param null $default
   * @param bool $strict_default_check
   *
   * @return null
   */
  public static function cut(&$array, $key, $default = null, $strict_default_check = false)
  {
    if( $strict_default_check )
      $value = !empty($array[$key]) ? $array[$key] : $default;
    else
      $value = isset($array[$key]) ? $array[$key] : $default;

    unset($array[$key]);

    return $value;
  }

  /**
   * Retrieves multiple keys from an array. If the key does not exist in the
   * array, the default value will be added instead.
   *     // Get the values "username", "password" from $_POST
   *     $auth  = Arr::extract($_POST, array('username', 'password'));
   *     $param = Arr::extract($_POST, $param);
   *
   * @param   array   array to extract keys from
   * @param   mixed   key or list of key names
   * @param   mixed   default value
   *
   * @return  mixed
   */
  public static function extract($array, $keys, $default = null)
  {
    $found = array();

    if( is_array($keys) )
      foreach($keys as $key)
        $found[$key] = isset($array[$key]) ? $array[$key] : $default;
    elseif( is_string($keys) )
      $found = isset($array[$keys]) ? $array[$keys] : $default;

    return $found;
  }

  /**
   * возвращаем первый эелемент массива
   * @param mixed $array
   *
   * @return mixed $first_value
   */
  public static function reset(array $array)
  {
    $item = reset($array);
    return $item;
  }

  /**
   *  Провеворка массива на наличие ключей
   * @param mixed $keys
   * @param array $search
   *
   * @return bool
   */
  public static function keysExists($keys, array $search)
  {
    $result = true;

    if( is_array($keys) )
    {
      foreach($keys as $value)
        if( !array_key_exists($value, $search) )
          return false;
    }
    else
      $result = array_key_exists($keys, $search);

    return $result;
  }

  /**
   *  Преобразуем объект данных в ассоциативный массив
   * @param  $obj
   *
   * @return array
   */
  public static function fromObj($obj)
  {
    if( is_object($obj) )
      return get_object_vars($obj);
    else if( is_array($obj) )
    {
      foreach($obj as $key => $value)
        $obj[$key] = call_user_func(__METHOD__, $value);

      return $obj;
    }
    else
      return $obj;
  }

  /**
   * проверим, пересекаются ли два массива
   * @param mixed $arr1
   * @param mixed $arr2
   *
   * @return mixed
   */
  public static function isIntersec(array $arr1 = array(), array $arr2 = array())
  {
    $intersec = array_intersect($arr1, $arr2);
    return empty($intersec);
  }

  /**
   * Выполняем trim над всеми элементами массива
   *
   * @param array  $array
   * @param string $charlist
   *
   * @return array $array
   */
  public static function trim($array, $charlist = '')
  {
    if( is_array($array) )
    {
      foreach($array as $key => $value)
      {
        if( !empty($charlist) )
          $array[$key] = Arr::trim($value, $charlist);
        else
          $array[$key] = Arr::trim($value);
      }
    }
    elseif( is_string($array) )
    {
      if( !empty($charlist) )
        $array = trim($array, $charlist);
      else
        $array = trim($array);
    }

    return $array;
  }

  /**
   * @static
   *
   * @param       $glue
   * @param array $pieces
   *
   * @return mixed
   */
  public static function implode($glue, array $pieces)
  {
    foreach($pieces as $key => $value)
      if( empty($value) )
        unset($pieces[$key]);

    return preg_replace("/\s+/", " ", implode($glue, $pieces));
  }

  public static function entitiesEncode($array, $params = array())
  {
    if( is_array($array) )
    {
      foreach($array as $key => $value)
      {
        $array[$key] = self::entitiesEncode($value, $params);
      }
    }
    elseif( is_string($array) )
    {
      $array = htmlspecialchars($array, ENT_QUOTES, 'cp1251');
    }

    return $array;
  }

  public static function entitiesDecode($array, $params = array())
  {
    if( is_array($array) )
    {
      foreach($array as $key => $value)
      {
        $array[$key] = self::entitiesDecode($value, $params);
      }
    }
    elseif( is_string($array) )
    {
      $array = htmlspecialchars_decode($array, ENT_QUOTES);
    }

    return $array;
  }

  public static function reflect($array)
  {
    return array_combine($array, $array);
  }

  /**
   * @param array $data
   *
   * @return array|mixed
   */
  public static function reduce(array $data)
  {
    if( is_array($data) )
      return self::reset($data);
    else
      return $data;
  }

  /**
   * @param $array
   * @param $key
   * @param $item
   */
  public static function push(&$array, $key, $item)
  {
    if( !isset($array[$key]) )
      $array[$key] = array();

    $array[$key][] = $item;
  }

  /**
   * Ассоциативное объединение массивов
   * @param array $a
   * @param array $b
   * @return array
   */
  public static function array_merge_assoc(array $a, array $b)
  {
    return array_diff_key($a, $b) + $b;
  }

  public static function divide(array $array, $countOfParts = 2)
  {
    if( !count($array) )
      return array();
    else
      return array_chunk($array, ceil(count($array) / $countOfParts));
  }

  /**
   * @param $array
   * @param $after
   * @param $item
   * @param $itemKey
   */
  public static function insertAfter(&$array, $after, $item, $itemKey)
  {
    $counter = 1;
    foreach($array as $key => $value)
    {
      if( $key === $after )
        break;

      $counter++;
    }

    $array_head = array_slice($array, 0, $counter);
    $array_tail = array_slice($array, $counter);
    $array      = array_merge($array_head, array($itemKey => $item), $array_tail);
  }
}