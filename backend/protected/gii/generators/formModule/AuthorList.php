<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 */
class AuthorList 
{
  private static $authors = array(
    'Alexey Tatarinov <tatarinov@shogo.ru>',
    'Sergey Glagolev <glagolev@shogo.ru>'
  );

  public static function getList()
  {
    return self::$authors;
  }

  public static function getByIndex($index)
  {
    return self::$authors[$index];
  }
} 