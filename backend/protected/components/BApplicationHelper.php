<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.components
 */
class BApplicationHelper
{
  /**
   * Удаление префикса класса
   *
   * @param string $className
   *
   * @return string mixed
   */
  public static function cutClassPrefix($className)
  {
    return preg_replace('/^'.BApplication::CLASS_PREFIX.'([A-Z])(.*)/', '$1$2', $className);
  }
}