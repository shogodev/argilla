<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.share
 */
class Utils
{
  public static function pre()
  {
    if( PHP_SAPI !== 'cli' )
    {
      echo '<div style="padding: 5px;margin: 5px;border: 1px solid #333333;background-color: #DFEEFF;color: #000000" id="debug">';
      echo '<pre>';
    }

    foreach( func_get_args() as $data )
      if( is_array($data) || is_object($data) )
        print_r($data);
      else
        var_dump($data);

    if( PHP_SAPI !== 'cli' )
    {
      echo '</pre>';
      echo "</div>";
    }
  }

  /**
   * @param string $str
   * @param bool $method_format
   *
   * @return string
   */
  public static function toCamelCaps($str, $method_format = false)
  {
    $arr  = explode("_", $str);
    $name = array_shift($arr);

    if( !$method_format )
      $name = ucfirst($name);

    if( count($arr) )
      foreach($arr as $value)
        $name .= ucfirst($value);

    return $name;
  }

  /**
   * Добавляет случайное число после имени файла
   *
   * @param string $filename
   *
   * @return string
   */
  public static function doCustomFilename($filename)
  {
    $ext      = pathinfo($filename, PATHINFO_EXTENSION);
    $filename = str_replace('.'.$ext, '', $filename);
    $filename = $filename . '_' . rand(1, 1000) . '.' . $ext;

    return $filename;
  }

  /**
   * Обрезаем текст до нужной длины по пробелу
   *
   * @param $str
   * @param integer $n
   *
   * @return string
   */
  public static function stripText($str, $n = 150)
  {
    if( strlen($str) > $n )
    {
      $str = substr($str, 0, $n + 1);
      $str = substr($str, 0, strrpos($str, " ") - $n - 1);
    }

    return $str;
  }

  /**
   * @param $date
   * @param string $defaultValue
   *
   * @return string
   */
  public static function dayBegin($date, $defaultValue = '')
  {
    return date("Y-m-d 00:00:00", strtotime($date ? $date : $defaultValue));
  }

  /**
   * @param $date
   * @param string $defaultValue
   *
   * @return string
   */
  public static function dayEnd($date, $defaultValue = '31.12.2999')
  {
    return date("Y-m-d 23:59:59", strtotime($date ? $date : $defaultValue));
  }

  public static function translite($text, $urlFormat = true)
  {
    $trans = array(
      'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'jo', 'ж' => 'zh', 'з' => 'z', 'и' => 'i',
      'й' => 'j', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f',
      'х' => 'x', 'ц' => 'c', 'ч' => 'ch', 'ш' => 'sh', 'щ' => 'th', 'ъ' => '', 'ь' => '', 'ы' => 'y', 'э' => 'e', 'ю' => 'ju', 'я' => 'ya',
      'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E', 'Ё' => 'JO', 'Ж' => 'ZH', 'З' => 'Z', 'И' => 'I',
      'Й' => 'J', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O', 'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F',
      'Х' => 'X', 'Ц' => 'C', 'Ч' => 'CH', 'Ш' => 'SH', 'Щ' => 'TH', 'Ъ' => '', 'Ь' => '', 'Ы' => 'Y', 'Э' => 'E', 'Ю' => 'JU', 'Я' => 'YA', ' ' => '_');

    $newStr = "";

    for($i = 0; $i < mb_strlen($text, 'UTF-8'); $i++)
    {
      $tmp = mb_substr($text, $i, 1, 'UTF-8');

      if( isset($trans[$tmp]) )
      {
        $newStr .= $trans[$tmp];
      }
      else
      {
        if( $urlFormat )
          $replaceCondition = preg_match("/[^\w]/", $tmp);
        else
          $replaceCondition = (ord($tmp) < 32) || (ord($tmp) > 126);

        if( $replaceCondition )
          $newStr .= '_';
        else
          $newStr .= $tmp;
      }
    }

    $newStr = preg_replace("/_+/", '_', $newStr);

    return $urlFormat ? mb_strtolower($newStr, 'UTF-8') : $newStr;
  }

  public static function unserialize($string)
  {
    return unserialize(preg_replace('!s:(\d+):"(.*)";!esmU', "'s:'.strlen('$2').':\"$2\";'", $string));
  }

  /**
   * Удаляем из строки запроса набор get параметров
   *
   * @param       $query
   * @param array $params
   *
   * @return string
   */
  public static function cutQueryParams($query, array $params)
  {
    $url = parse_url($query);

    if( !empty($url['query']) )
    {
      parse_str($url['query'], $query);
      foreach($params as $param)
        if( isset($query[$param]) )
          unset($query[$param]);

      $url = $url['path'].(!empty($query) ? "?".http_build_query($query) : "");
    }
    else
      $url = $url['path'];

    return $url;
  }

  public static function buildUrl(array $components)
  {
    $url  = isset($components['path']) ? $components['path'] : "";
    $url .= isset($components['query']) ? "?".$components['query'] : "";
    $url .= isset($components['fragment']) ? "#".$components['fragment'] : "";

    return $url;
  }

  public static function generatePassword($length = 8)
  {
    $randKey  = "";
    $keyChars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_";
    $max      = strlen($keyChars) - 1;

    for($i = 0; $i <= $length; $i++)
      $randKey .= $keyChars{rand(0, $max)};

    return $randKey;
  }

  /**
   * Получение имени таблицы по имени модели в виде {{class_name}}
   *
   * @param string $class
   *
   * @return string
   */
  public static function getTableNameFromClass($class)
  {
    return '{{'.strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $class)).'}}';
  }
}