<?php

/**
 * User: Nikita Melnikov <melnikov@shogo.ru>
 * Date: 12/3/12
 */
class RedirectType extends CModel
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

  public function attributeNames()
  {
    return array(
      'name' => 'Название',
    );
  }
}