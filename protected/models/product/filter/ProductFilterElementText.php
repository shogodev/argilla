<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>, Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.product.filter
 */

class ProductFilterElementText extends ProductFilterElement
{
  public function addPropertyCondition(CDbCriteria $criteria)
  {
    $criteria->addSearchCondition($this->id, $this->selected);
  }

  public function getParameterCondition()
  {
    $criteria = new CDbCriteria();
    $criteria->compare('param_id', '='.$this->id);
    $criteria->addSearchCondition('value', $this->selected);

    return $criteria;
  }
}