<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 *
 * @property integer $id
 * @property string $name
 * @property string $description
 */
class ProductDump extends FActiveRecord
{
  const NOT_AVAILABLE = 0;

  const AVAILABLE = 1;

  const AVAILABLE_ORDER = 2;

  /**
   * @var ProductDump[]
   */
  private static $list;

  /**
   * @return ProductDump[]
   */
  private static function getList()
  {
    if( is_null(self::$list) )
      self::$list = self::model()->findAll();

    return self::$list;
  }

  /**
   * @param integer $id
   *
   * @return string
   */
  public static function getName($id)
  {
    $list = self::getList();

    return $list[$id]->name;
  }

  /**
   * @param integer $id
   *
   * @return string
   */
  public static function getDescription($id)
  {
    $list = self::getList();

    return $list[$id]->description;
  }
}