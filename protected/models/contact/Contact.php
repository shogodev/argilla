<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.contact
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
 * @property ContactGroup[] $groups
 * @property ContactTextBlock[] $blocks
 */
class Contact extends FActiveRecord
{
  public $image;

  public $imageBig;

  protected $groups;

  public function relations()
  {
    return array(
      'blocks' => array(self::HAS_MANY, 'ContactTextBlock', 'contact_id', 'order'=>'b.position ASC', 'alias'=>'b'),
    );
  }

  public function defaultScope()
  {
    $alias = $this->getTableAlias(false, false);

    return array(
      'condition' => $alias.'.visible=1',
    );
  }

  public function getFields($groupName)
  {
    if( $this->groups === null )
      $this->groups = ContactGroup::model()->findAllByAttributes(array('contact_id' => $this->id));

    foreach($this->groups as $group)
      if( $group->sysname === $groupName )
        return $group->fields;

    return array();
  }

  protected function afterFind()
  {
    if( !empty($this->img) )
      $this->image = new FSingleImage($this->img, 'contact');

    if( !empty($this->img_big) )
      $this->imageBig = new FSingleImage($this->img_big, 'contact');

    parent::afterFind();
  }
}