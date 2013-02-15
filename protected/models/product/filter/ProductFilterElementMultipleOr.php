<?php
/**
 * User: tatarinov
 * Date: 12.12.12
 */
class ProductFilterElementMultipleOr extends ProductFilterElement
{
  public function inAvailableValues($availableValues)
  {
    if( !isset($availableValues[$this->id]) )
      return false;

    $selected = $availableValues[$this->id];

    if( !is_array($selected) )
      $selected = array($selected);

    if( !empty($this->selected) )
      $intersect = array_intersect_key($this->selected, $selected);

    return !empty($intersect);
  }

  public function addPropertyCondition(CDbCriteria $criteria)
  {
    $criteria->addInCondition($this->id, array_keys($this->selected));
  }

  public function addParameterCondition(CDbCriteria $parameterCriteria)
  {
    $criteria = new CDbCriteria();
    $criteria->compare('param_id', '='.$this->id);

    $param = $this->selected;

    if( !is_array($param) )
      $param = array($param => 'on');

    $innerCriteria = new CDbCriteria();
    foreach($param as $variant => $value)
      if( $value )
        $innerCriteria->compare('variant_id', '='.$variant, false, 'OR');

    $criteria->mergeWith($innerCriteria, true);

    $parameterCriteria->mergeWith($criteria, false);
  }

  public function isSelectedItems($itemId)
  {
    if( isset($this->parent->state[$this->id]) )
    {
      if( isset($this->parent->state[$this->id][$itemId]) )
        return true;
    }

    return false;
  }
}
?>