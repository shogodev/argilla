<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 *
 * @property integer $id
 * @property string $name
 * @property string $group
 */
class Tag extends CActiveRecord
{
  public function tableName()
  {
    return '{{tag}}';
  }

  public static function model($className = __CLASS__)
  {
    return parent::model(get_called_class());
  }
}