<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2016 Shogo
 * @license http://argilla.ru/LICENSE
 */
class FacetModificationPropertyIterator extends FacetPropertyIterator
{
  protected function getCriteria($productIdList)
  {
    $criteria = new CDbCriteria();
    $criteria->select = CMap::mergeArray(array('t.id'), $this->propertyList);
    $criteria->distinct = true;
    $criteria->join = 'JOIN '.Yii::app()->db->schema->quoteTableName($this->assignmentTable).' AS a ON a.product_id = t.parent';
    $criteria->addCondition('NOT t.parent IS NULL');

    if( !is_null($productIdList) )
      $criteria->addInCondition('t.id', $productIdList);

    return $criteria;
  }
}