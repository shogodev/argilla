<?php

/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.seo
 *
 * @method static BRedirectType model(string $class = __CLASS__)
 */
class BRedirectType extends CModel
{
  const TYPE_REPLACE = 1;
  const TYPE_301     = 301;
  const TYPE_302     = 302;
  const TYPE_404     = 404;

  /**
   * @return array
   */
  public static function getList()
  {
    return array(
      self::TYPE_REPLACE => 'Подмена url',
      self::TYPE_301     => '301 - Moved permanently',
      self::TYPE_302     => '302 - Moved temporarily',
      self::TYPE_404     => '404 - Not found',
    );
  }

  /**
   * @return array
   */
  public function attributeNames()
  {
    return array(
      'name' => 'Название',
    );
  }
}