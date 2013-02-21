<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.contact.models
 *
 * @method static BContactGroup model(string $class = __CLASS__)
 *
 * @property integer $id
 * @property integer $contact_id
 * @property string $name
 * @property integer $position
 * @property integer $visible
 * @property string $sysname
 *
 * @property BContact $contact
 * @property BContactField[] $contactFields
 */
class BContactGroup extends BActiveRecord
{
  /**
   * Получение группы полей по системному имени (поле sysname)
   *
   * @param string $key
   *
   * @return BContactGroup
   */
  public static function getByKey($key)
  {
    return self::model()->findByAttributes(array('sysname' => $key));
  }

  /**
   * @return array
   */
  public function rules()
  {
    return array(
      array('contact_id, name, sysname', 'required'),
      array('id, contact_id, position, visible', 'numerical', 'integerOnly'=>true),
      array('name', 'length', 'max'=>45),
    );
  }

  /**
   * @return array
   */
  public function relations()
  {
    return array(
      'contact' => array(self::BELONGS_TO, 'BContact', 'contact_id'),
      'contactFields' => array(self::HAS_MANY, 'BContactField', 'group_id', 'order'=>'j.position ASC', 'alias'=>'j'),
    );
  }

  /**
   * Перед удалением группы, удаляем все дочерние поля
   *
   * @return boolean
   */
  public function beforeDelete()
  {
    if( !empty($this->contactFields) )
    {
      foreach( $this->contactFields as $field )
      {
        $field->delete();
      }
    }

    return parent::beforeDelete();
  }
}