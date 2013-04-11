<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>, Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.product.filter
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

  public function getParameterCondition()
  {
    $criteria = new CDbCriteria();
    $criteria->compare('param_id', '='.$this->id);

    if( !is_array($this->selected) )
      $variants = array($this->selected => $this->selected);
    else
      $variants = array_keys($this->selected);

    $criteria->addInCondition('variant_id', $variants);

    return $criteria;
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