<?php

/**
 * @data 17.09.12
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @package Contact
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
 * @property ContactFgroup $group
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
      'group' => array(self::BELONGS_TO, 'ContactFgroup', 'group_id'),
    );
  }

  public function __toString()
  {
    return $this->value;
  }
}