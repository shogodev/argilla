<?php
/**
 * User: tatarinov
 * Date: 11.12.12
 */
class ProductFilterElementList extends ProductFilterElement
{
  public function addPropertyCondition(CDbCriteria $criteria)
  {
    $criteria->compare($this->id, '='.$this->selected);
  }

  public function addParameterCondition(CDbCriteria $parameterCriteria)
  {
    $criteria = new CDbCriteria();
    $criteria->compare('param_id', '='.$this->id);

    $param = $this->selected;

    if( !is_array($param) )
      $param = array($param => 'on');

    foreach($param as $variant => $value)
      if( $value )
        $criteria->compare('variant_id', '='.$variant);

    $parameterCriteria->mergeWith($criteria, false);
  }
}
?>