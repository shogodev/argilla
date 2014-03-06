<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>, Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.share.helpers
 */
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
    {
      return !empty($array[$key]) ? $array[$key] : $default;
    }
    else
    {
      return isset($array[$key]) ? $array[$key] : $default;
    }
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
    {
      $value = !empty($array[$key]) ? $array[$key] : $default;
    }
    else
    {
      $value = isset($array[$key]) ? $array[$key] : $default;
    }

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
    {
      foreach($keys as $key)
      {
        $found[$key] = isset($array[$key]) ? $array[$key] : $default;
      }
    }
    else
    {
      $found = isset($array[$keys]) ? $array[$keys] : $default;
    }

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
   * возвращаем последний эелемент массива
   * @param mixed $array
   *
   * @return mixed $last_value
   */
  public static function end(array $array)
  {
    $item = end($array);
    return $item;
  }

  /**
   * Провеворка массива на наличие ключей
   * @param array $search
   * @param mixed $keys
   *
   * @return bool
   */
  public static function keysExists(array $search, $keys)
  {
    $result = true;

    if( is_array($keys) )
    {
      foreach($keys as $value)
      {
        if( !array_key_exists($value, $search) )
        {
          return false;
        }
      }
    }
    else
    {
      $result = array_key_exists($keys, $search);
    }

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
    {
      return get_object_vars($obj);
    }
    else if( is_array($obj) )
    {
      foreach($obj as $key => $value)
      {
        $obj[$key] = call_user_func(__METHOD__, $value);
      }

      return $obj;
    }
    else
    {
      return $obj;
    }
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
    return !empty($intersec);
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
        {
          $array[$key] = self::trim($value, $charlist);
        }
        else
        {
          $array[$key] = self::trim($value);
        }
      }
    }
    elseif( is_string($array) )
    {
      if( !empty($charlist) )
      {
        $array = trim($array, $charlist);
      }
      else
      {
        $array = trim($array);
      }
    }

    return $array;
  }

  /**
   * @static
   *
   * @param       $glue
   * @param array $array
   *
   * @return mixed
   */
  public static function implode(array $array, $glue)
  {
    foreach($array as $key => $value)
    {
      if( empty($value) )
      {
        unset($array[$key]);
      }
    }

    return preg_replace("/\s+/", " ", implode($glue, $array));
  }

  public static function reflect($array)
  {
    return array_combine($array, $array);
  }

  /**
   * @param mixed $data
   *
   * @return mixed
   */
  public static function reduce($data)
  {
    if( is_array($data) )
    {
      return self::reset($data);
    }
    else
    {
      return $data;
    }
  }

  /**
   * Объединение ассоциативных массивов.
   * В отличие от CMap::mergeArray перекрывает целочисленные ключи, а не увеличивает индекс элементов
   * @param array $a
   * @param array $b
   * @return array
   */
  public static function mergeAssoc(array $a, array $b)
  {
    $args = func_get_args();
    $res  = array_shift($args);

    while( !empty($args) )
    {
      $next = array_shift($args);
      foreach($next as $k => $v)
      {
        if( is_array($v) && isset($res[$k]) && is_array($res[$k]) )
          $res[$k] = self::mergeAssoc($res[$k], $v);
        else
          $res[$k] = $v;
      }
    }

    return $res;
  }

  public static function divide(array $array, $countOfParts = 2)
  {
    if( !count($array) )
    {
      return array();
    }
    else
    {
      return array_chunk($array, ceil(count($array) / $countOfParts));
    }
  }

  /**
   * @param $array
   * @param $itemKey
   * @param $item
   */
  public static function push(&$array, $itemKey, $item)
  {
    if( !isset($array[$itemKey]) )
    {
      $array[$itemKey] = array();
    }

    $array[$itemKey][] = $item;
  }

  /**
   * @param $array
   * @param $after
   * @param $item
   * @param $itemKey
   */
  public static function insertAfter(&$array, $itemKey, $item, $after)
  {
    $counter = 1;
    foreach($array as $key => $value)
    {
      if( $key === $after )
      {
        break;
      }

      $counter++;
    }

    $array_head = array_slice($array, 0, $counter, true);
    $array_tail = array_slice($array, $counter, null, true);
    $array      = self::mergeAssoc($array_head, array($itemKey => $item));
    $array      = self::mergeAssoc($array, $array_tail);
  }

  /**
   * @param $array - масив элементы которого нужно отфильтровать
   * @param string|array $keys - ключ или массив ключей элементов $array которые нужно сравнить с $value
   * @param $value
   * @param $condition - OR или AND условие срамнения нескольких ключей
   *
   * @return array
   */
  public static function filter($array, $keys, $value, $condition = 'OR')
  {
    $keys = !is_array($keys) ? array($keys) : $keys;
    $condition = strtolower($condition);

    return array_filter($array, function($element) use ($keys, $value, $condition)
    {
      foreach($keys as $key)
      {
        if( !isset($element[$key]) )
          continue;

        $result = $element[$key] === $value ? true : false;

        if( $condition == 'or' && $result )
          return true;

        if( $condition == 'and' && !$result )
          return false;
      }

      return false;
    });
  }
}