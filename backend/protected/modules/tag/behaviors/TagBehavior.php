<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 * Пример подключения
 *
 * 'tag' => array('class' => 'backend.modules.tag.behaviors.TagBehavior', 'group' => 'product'),
 */
Yii::import('backend.modules.tag.models.*');
/**
 * Class TagBehavior
 */
class TagBehavior extends CBehavior
{
  public $group;

  public $itemAttribute = 'id';

  /**
   * @return Tag[]
   */
  public function getTags()
  {
    $criteria = new CDbCriteria();
    $criteria->compare('t.`group`', $this->group);
    $criteria->compare('item_id', $this->owner->{$this->itemAttribute});
    $criteria->with = array('tag');
    $criteria->order = 'tag.name';

    return CHtml::listData(TagItem::model()->findAll($criteria), 'tag_id', 'tag');
  }

  /**
   * @param $tagId
   *
   * @return array
   */
  public function getTagItemIds($tagId)
  {
    $criteria = new CDbCriteria();
    $criteria->select = 'item_id';
    $criteria->compare('`group`', $this->group);
    $criteria->compare('tag_id', $tagId);

    $command = Yii::app()->db->commandBuilder->createFindCommand(TagItem::model()->tableName(), $criteria);

    return $command->queryColumn();
  }
}