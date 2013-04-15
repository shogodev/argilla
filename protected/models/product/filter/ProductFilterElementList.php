<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>, Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.product.filter
 */

class ProductFilterElementList extends ProductFilterElement
{
  public function addPropertyCondition(CDbCriteria $criteria)
  {
    $criteria->compare($this->id, '='.$this->selected);
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
}