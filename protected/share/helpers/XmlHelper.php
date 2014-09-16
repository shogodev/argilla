<?php
/**
 * @author Vladimir Utenkov <utenkov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.share.helpers
 */
class XmlHelper
{
  /**
   * @param $string
   *
   * @return string
   */
  public static function escape($string)
  {
    if( is_array($string) )
    {
      foreach($string as $key => $value)
      {
        $string[$key] = self::escape($value);
      }
    }

    return htmlspecialchars(trim($string));
  }
}