<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 *
 * @method static BAssignmentContent model(string $class = __CLASS__)
 *
 * @property int $id
 * @property int $section_id
 * @property int $type_id
 * @property int $category_id
 * @property int $collection_id
 * @property int $content
 * @property int $visible
 *
 * @property BProductSection $section
 * @property BProductType $type
 * @property BProductCategory $category
 * @property BProductCollection $collection
 */
class BAssignmentContent extends BActiveRecord
{
  public function tableName()
  {
    return '{{product_assignment_content}}';
  }

  public function rules()
  {
    return array(
      array('location', 'required'),
      array('visible, section_id, type_id, category_id, collection_id', 'numerical', 'integerOnly' => true),
      array('content', 'safe')
    );
  }

  public function relations()
  {
    return array(
      'section' => array(self::BELONGS_TO, 'BProductSection', 'section_id'),
      'category' => array(self::BELONGS_TO, 'BProductCategory', 'category_id'),
      'type' => array(self::BELONGS_TO, 'BProductType', 'type_id'),
      'collection' => array(self::BELONGS_TO, 'BProductCollection', 'collection_id'),
    );
  }

  public function getAssignmentModelName($type)
  {
    $name = null;

    if( isset($this->relations()[$type]) )
    {
      if( !empty($this->{$type}) )
        $name = $this->{$type}->name;
    }

    return $name;
  }

  /**
   * @return array
   */
  public function attributeLabels()
  {
    return CMap::mergeArray(parent::attributeLabels(), array(
      'location' => 'Размещение'
    ));
  }

  public function getSearchCriteria(CDbCriteria $criteria)
  {
    $criteria = new CDbCriteria();

    $criteria->compare('section_id', $this->section_id);
    $criteria->compare('type_id', $this->type_id);
    $criteria->compare('category_id', $this->category_id);
    $criteria->compare('collection_id', $this->collection_id);
    $criteria->compare('location', $this->location);
    $criteria->compare('visible', $this->visible);

    return $criteria;
  }
}