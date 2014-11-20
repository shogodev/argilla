<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components.redirect
 */
class RedirectHelper
{
  const REGEXP_START_CHAR = '#';

  const TYPE_REPLACE = 1;
  const TYPE_301     = 301;
  const TYPE_302     = 302;
  const TYPE_404     = 404;

  /**
   * @return array
   */
  public static function getTypes()
  {
    return array(
      self::TYPE_REPLACE => 'Подмена url',
      self::TYPE_301     => '301 - Moved permanently',
      self::TYPE_302     => '302 - Moved temporarily',
      self::TYPE_404     => '404 - Not found',
    );
  }

  /**
   * @param $expression
   *
   * @return bool
   */
  public static function isRegExp($expression)
  {
    return stripos($expression, self::REGEXP_START_CHAR) === 0;
  }

  public static function isAbsolute($url)
  {
    return stripos($url, 'http://') === 0;
  }

  /**
   * @param string $url
   *
   * @return bool
   */
  public static function needTrailingSlash($url)
  {
    return !preg_match("/.+\.\w+$/", $url) && substr($url, -1, 1) !== '/';
  }

  /**
   * @param string $url
   * @param array $matches
   *
   * @return bool
   */
  public static function scriptNamePresent($url, &$matches)
  {
    return preg_match('/^\/index\.php(.*)/', $url, $matches) > 0;
  }
}