<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2016 Shogo
 * @license http://argilla.ru/LICENSE
 */
Yii::import('frontend.share.components.SqlIterator');

class FacetPropertyIterator extends SqlIterator
{
  protected $productTable = '{{product}}';

  protected $assignmentTable = '{{product_assignment}}';

  protected $propertyList;

  public function __construct(array $propertyList, $chunkSize, array $productIdList = null)
  {
    $this->propertyList = $propertyList;

    parent::__construct($this->getCriteria($productIdList), $this->productTable, $chunkSize);
  }

  public function current()
  {
    $row = parent::current();

    return $this->getAttributeList($row);
  }

  public function getAttributeList($row)
  {
    $getAttributeList = array();

    foreach($this->propertyList as $property)
    {
      if( empty($row[$property]) )
        continue;

      $getAttributeList[] = array(
        'product_id' => $row['id'],
        'param_id' => $property,
        'value' => $row[$property]
      );
    }

    return $getAttributeList;
  }

  /**
   * @param $productIdList
   *
   * @return CDbCriteria
   */
  protected function getCriteria($productIdList)
  {
    $criteria = new CDbCriteria();
    $criteria->select = CMap::mergeArray(array('t.id'), $this->propertyList);
    $criteria->distinct = true;
    $criteria->join = 'JOIN '.Yii::app()->db->schema->quoteTableName($this->assignmentTable).' AS a ON a.product_id = t.id';
    $criteria->addCondition('t.parent IS NULL');

    if( !is_null($productIdList) )
      $criteria->addInCondition('t.id', $productIdList);

    return $criteria;
  }
}