<?php
/**
 * @author    Vladimir Utenkov <utenkov@shogo.ru>
 * @link      https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license   http://argilla.ru/LICENSE
 */
class XmlHelper
{
  /**
   * @param string $value
   *
   * @return string
   */
  public static function escape($value)
  {
    return htmlspecialchars(strip_tags(trim($value)));
  }
}