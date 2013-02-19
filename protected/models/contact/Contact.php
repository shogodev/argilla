<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @package Contact
 *
 * @property integer $id
 * @property string $title
 * @property string $url
 * @property string $address
 * @property string $notice
 * @property string $img
 * @property string $img_big
 * @property string $map
 * @property integer $visible
 *
 * @property ContactGroup[] $contactGroups
 */
class Contact extends FActiveRecord
{
  public $image;

  public function tableName()
  {
    return '{{contact}}';
  }

  public function relations()
  {
    return array(
      'contactGroups' => array(self::HAS_MANY, 'ContactGroup', 'contact_id'),
      'textblocks'    => array(self::HAS_MANY, 'ContactTextBlock', 'contact_id', 'order'=>'j.position ASC', 'alias'=>'j'),
    );
  }

  protected function afterFind()
  {
    $this->image = new FSingleImage($this->img, 'contact');
    parent::afterFind();
  }
}