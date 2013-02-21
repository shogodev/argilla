<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.contact.models
 *
 * @method static BContactField model(string $class = __CLASS__)
 *
 * @property integer $id
 * @property integer $contact_id
 * @property integer $group_id
 * @property string $value
 * @property string $description
 * @property integer $position
 * @property integer $visible
 *
 * @property BContact $contact
 * @property BContactGroup $group
 */
class BContactField extends BActiveRecord
{
  /**
   * @return array
   */
  public function rules()
  {
    return array(
      array('group_id, value', 'required'),
      array('id, group_id, position, visible', 'numerical', 'integerOnly'=>true),
      array('value', 'length', 'max'=>512),
      array('description', 'length', 'max'=>128),
    );
  }

  /**
   * @return array
   */
  public function relations()
  {
    return array(
      'group' => array(self::BELONGS_TO, 'ContactFgroup', 'group_id'),
    );
  }
}