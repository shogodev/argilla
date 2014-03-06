<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.contact
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

  public function defaultScope()
  {
    $alias = $this->getTableAlias(false, false);

    return array(
      'condition' => $alias.'.visible = 1'
    );
  }

  public function relations()
  {
    return array(
      'fields' => array(self::HAS_MANY, 'ContactField', 'group_id', 'order'=>'f.position ASC', 'alias'=>'f'),
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