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
 * @property integer $group_id
 * @property string $value
 * @property string $description
 * @property integer $position
 * @property integer $visible
 *
 * The followings are the available model relations:
 * @property Contact $contact
 * @property ContactGroup $group
 */
class ContactField extends FActiveRecord
{
  public function tableName()
  {
    return '{{contact_field}}';
  }

  public function relations()
  {
    return array(
      'group' => array(self::BELONGS_TO, 'ContactGroup', 'group_id'),
    );
  }

  public function getClearPhone()
  {
    return ViewHelper::getClearPhone($this->value.$this->description);
  }

  public function __toString()
  {
    return $this->value;
  }
}