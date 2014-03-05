<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components.redirect
 */
class RedirectHelper
{
  const REGEXP_START_CHAR = '#';

  /**
   * @param $expression
   *
   * @return bool
   */
  public static function isRegExp($expression)
  {
    return stripos($expression, RedirectHelper::REGEXP_START_CHAR) === 0;
  }

  /**
   * @param string $template
   *
   * @return string
   */
  public static function prepareReplacement($template)
  {
    $template = preg_replace_callback("/\([^\)]+\)/", function(){
      static $position = 1;
      return '$'.$position++;
    }, trim($template, '#'));

    return $template;
  }
}