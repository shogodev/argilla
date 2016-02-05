<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 */
class TagModule extends BModule
{
  const KEY_PRODUCT = 'product';

  public static $tagGroupList = array(
    self::KEY_PRODUCT => 'Продукты'
  );

  public $defaultController = 'BTag';

  public $name = 'Тэги';

  public $enabled = false;
}