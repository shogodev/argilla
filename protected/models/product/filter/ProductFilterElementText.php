<?php
/**
 * User: tatarinov
 * Date: 13.12.12
 */
class ProductFilterElementText extends ProductFilterElement
{

  public function addPropertyCondition(CDbCriteria $criteria)
  {
    $criteria->addSearchCondition($this->id, $this->selected);
  }

  public function addParameterCondition(CDbCriteria $parameterCriteria)
  {
    $criteria = new CDbCriteria();
    $criteria->compare('param_id', '='.$this->id);
    $criteria->addSearchCondition('value', $this->selected);
    $parameterCriteria->mergeWith($criteria, false);
  }
}
?>