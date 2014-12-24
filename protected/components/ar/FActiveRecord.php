<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components.ar
 */
class FActiveRecord extends CActiveRecord
{
  /**
   * @return string model table name
   */
  public static function table()
  {
    $model = parent::model(get_called_class());
    return $model->tableName();
  }

  /**
   * @param string $className
   *
   * @return FActiveRecord|CActiveRecord
   */
  public static function model($className = __CLASS__)
  {
    return parent::model(get_called_class());
  }

  /**
   * @return string
   */
  public function tableName()
  {
    return '{{'.Utils::toSnakeCase(get_class($this)).'}}';
  }

  public function attributeLabels()
  {
    return array(
      'name' => 'Имя',
      'content' => 'Сообщение',
    );
  }
}