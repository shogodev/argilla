<?php

/**
 * @date 17.09.12
 * @author Nikita Melnikov
 * @package Contact
 *
 * @property integer $id
 * @property integer $contact_id
 * @property string $name
 * @property integer $position
 * @property integer $visible
 * @property string $sysname
 *
 * The followings are the available model relations:
 * @property Contact $contact
 * @property ContactField[] $contactFields
 */
class ContactGroup extends FActiveRecord
{
  protected static $groups = array();

  public function tableName()
  {
    return '{{contact_group}}';
  }

  public function relations()
  {
    return array(
      'contact' => array(self::BELONGS_TO, 'Contact', 'contact_id'),
      'fields' => array(self::HAS_MANY, 'ContactField', 'group_id', 'order'=>'j.position ASC', 'alias'=>'j'),
    );
  }

  /**
   * Получение группы полей по системному имени (поле sysname)
   *
   * @static
   *
   * @param string $key
   *
   * @return ContactGroup
   */
  public static function getByKey($key)
  {
    if( empty(self::$groups[$key]) )
      self::$groups[$key] = self::model()->findByAttributes(array('sysname' => $key));

    return self::$groups[$key];
  }
}