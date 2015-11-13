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
class BProductDump extends BActiveRecord
{
  const AVAILABLE = 1;

  const AVAILABLE_ORDER = 2;

  const NOT_AVAILABLE = 0;

  static private $list;

  public static function listData($key = 'id', $value = 'name', CDbCriteria $criteria = null)
  {
    $index = serialize(array($key, $value, $criteria) );

    if( isset(self::$list[$index]) )
      return self::$list[$index];

    $value = function($model) {
      /**
       * @var BProductDump $model
       */
      return $model->name.' '.$model->description;
    };

    self::$list[$index] = parent::listData($key, $value, $criteria);

    return self::$list[$index];
  }
}