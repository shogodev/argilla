<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 */
/**
 * Class TagItem
 *
 * @property string $item_id
 * @property integer $tag_id
 * @property string $group
 * @property Tag $tag
 */
class TagItem extends CActiveRecord
{
  public function tableName()
  {
    return '{{tag_item}}';
  }

  public static function model($className = __CLASS__)
  {
    return parent::model(get_called_class());
  }

  public function rules()
  {
    return array(
      array('item_id, tag_id', 'required'),
      array('tag_id', 'numerical', 'integerOnly' => true),
      array('item_id', 'length', 'max' => 10),
      array('group', 'length', 'max' => 255),
      array('item_id, tag_id', 'safe', 'on' => 'search'),
    );
  }

  public function relations()
  {
    return array(
      'tag' => array(self::BELONGS_TO, 'Tag', 'tag_id'),
    );
  }

  /**
   * @param CDbCriteria $criteria
   *
   * @return CDbCriteria
   */
  protected function getSearchCriteria(CDbCriteria $criteria)
  {
    $criteria->compare('item_id', $this->item_id, true);
    $criteria->compare('tag_id', $this->tag_id);

    return $criteria;
  }
}