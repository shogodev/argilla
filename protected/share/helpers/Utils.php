<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.share.helpers
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
   * @param bool $methodFormat
   *
   * @return string
   */
  public static function toCamelCase($str, $methodFormat = false)
  {
    $arr  = explode("_", $str);
    $name = array_shift($arr);

    if( !$methodFormat )
      $name = ucfirst($name);

    if( count($arr) )
      foreach($arr as $value)
        $name .= ucfirst($value);

    return $name;
  }

  /**
   * @param string $class
   * @return string
   */
  public static function toSnakeCase($class)
  {
    return strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $class));
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
    if( mb_strlen($str, 'UTF-8') > $n )
    {
      $str = mb_substr($str, 0, $n + 1, 'UTF-8');
      $str = mb_substr($str, 0, mb_strrpos($str, " ", 'UTF-8') - $n - 1, 'UTF-8');
      $str = trim(trim($str), "., ").'...';
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

  public static function buildUrl(array $parts)
  {
    $url  = isset($parts['scheme']) ? $parts['scheme'] . '://' : '';
    $url .= isset($parts['host']) ? $parts['host'] : '';
    $url .= isset($parts['user']) ? $parts['user'] . (isset($parts['pass'])) ? ':' . $parts['pass'] : '' .'@' : '';
    $url .= isset($parts['port']) ? ':' . $parts['port'] : '';
    $url .= isset($parts['path']) ? $parts['path'] : '';
    $url .= !empty($parts['query']) ? '?' . (is_array($parts['query']) ? http_build_query($parts['query']) : $parts['query']) : '';
    $url .= isset($parts['fragment']) ? '#' . $parts['fragment'] : '';

    return $url;
  }

  /**
   * Преобразуем ссылку к стандартному формату
   *
   * @param $url
   *
   * @return string
   */
  public static function normalizeUrl($url)
  {
    $url = rtrim($url, '/');
    $url = str_replace('/?', '?', $url);

    $components = parse_url(rtrim($url, '/'));

    if( !isset($components['path']) )
      $components['path'] = '';

    $components['path'] .= preg_match("/.+\.\w+$/", $components['path']) ? "" : '/';
    $components['path']  = preg_replace("/\/+/", "/", $components['path']);

    return self::buildUrl($components);
  }

  /**
   * Преобразуем абсолютную ссылку в относительную
   *
   * @param string $url
   *
   * @return string
   */
  public static function getRelativeUrl($url)
  {
    $parts = Arr::extract(parse_url($url), array('path', 'query', 'fragment'));
    $parts['path'] = self::normalizeUrl($parts['path']);

    return self::buildUrl($parts);
  }

  public static function generatePassword($length = 8)
  {
    $randKey  = "";
    $keyChars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_";
    $max      = strlen($keyChars) - 1;

    for($i = 0; $i < $length; $i++)
      $randKey .= $keyChars{rand(0, $max)};

    return $randKey;
  }

  /**
   * Возводит первый символ строки в верхний регистр
   * @param $string
   * @return string
   */
  public static function ucfirst($string)
  {
    return mb_strlen($string) > 1 ? mb_strtoupper(mb_substr($string, 0, 1)).mb_substr($string, 1) : mb_strtoupper(mb_substr($string, 0, 1));
  }

  /**
   * Приводит первый символ строки к нижнему регистру
   * @param $string
   * @return string
   */
  public static function lcfirst($string)
  {
    return mb_strlen($string) > 1 ? mb_strtolower(mb_substr($string, 0, 1)).mb_substr($string, 1) : mb_strtolower(mb_substr($string, 0, 1));
  }

  /**
   * Возвращает домен
   * @param integer $level если указан уровень, то домен обрезается до указанного уровня
   * @return mixed|string
   */
  public static function getDomain($level = null)
  {
    $domain = str_replace('http://', '', Yii::app()->request->getHostInfo());

    $elements = explode('.', $domain);

    if( $level === null || count($elements) < $level )
      return $domain;

    return implode('.', array_slice($elements, $level * -1));
  }

  /**
   * @param $number
   * @param array|string $titles
   * @return string
   */
  public static function plural($number, $titles = array())
  {
    if( !is_array($titles) )
    {
      $delimiter = strpos($titles, '|') !== false ? '|' : ',';
      $titles = explode($delimiter, $titles);
    }

    return Yii::t('app', implode('|', $titles), $number);
  }

  /**
   * @param string $date date in YYYY-MM-DD format
   *
   * @return bool
   */
  public static function dateUntil($date)
  {
    return strtotime('now') < strtotime($date);
  }

  /**
   * @param string $dateFrom date in YYYY-MM-DD format
   * @param string $dateTo date in YYYY-MM-DD format
   *
   * @return bool
   */
  public static function dateBetween($dateFrom, $dateTo)
  {
    return (strtotime('now') > strtotime($dateFrom)) && (strtotime('now') < strtotime($dateTo));
  }

  /**
   * @param CModel $class
   *
   * @return string
   */
  public static function modelToSnakeCase(CModel $class)
  {
    return self::toSnakeCase(get_class($class));
  }

  /**
   * Результат сравнения 2-x объектов или массивов
   * @param $a
   * @param $b
   *
   * @return bool
   */
  public static function compareObjects($a, $b)
  {
    if( (is_array($a) && is_array($b)) || (is_scalar($a) && is_scalar($b)) )
      return $a == $b;

    if( is_object($a) && is_object($b) )
      return serialize($a) == serialize($b);

    return false;
  }

  /**
   * Отдает ответ клиенту с продолжением работы скрипта (работает тольео на php-fpm)
   */
  public static function finishRequest()
  {
    if( function_exists('fastcgi_finish_request') )
    {
      session_write_close();
      if( !fastcgi_finish_request() )
      {
        throw new CException('Ошибка вызова fastcgi_finish_request!');
      }
    }
  }

  /**
   * Увеличивает время жизни скрипта
   * @param int $timeLimitMinutes
   * @param bool $ignoreUserAbort
   */
  public static function longLife($timeLimitMinutes = 0, $ignoreUserAbort = true)
  {
    set_time_limit($timeLimitMinutes * 60);
    ignore_user_abort($ignoreUserAbort);
  }
}