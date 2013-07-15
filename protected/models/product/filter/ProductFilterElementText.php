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
  public function propertyCondition($value)
  {
    $criteria = new CDbCriteria();
    $criteria->addSearchCondition($this->id, $value);

    return $criteria;
  }

  public function parameterCondition($value)
  {
    $criteria = new CDbCriteria();
    $criteria->compare('param_id', '='.$this->id);
    $criteria->addSearchCondition('value', $value);

    return $criteria;
  }
}