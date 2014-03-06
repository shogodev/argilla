<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.product.filter
 */
class ProductFilterElementGroupValues extends ProductFilterElement
{
  public $groupValues;

  public function prepareAvailableValues($value)
  {
    if( !empty($value) )
    {
      $availableValues = array();

      foreach($this->groupValues as $groupId => $values)
      {
        if( isset($values[$value]) )
          $availableValues[$groupId] = $groupId;
      }

      if( !empty($availableValues) )
        return $availableValues;
    }

    return $value;
  }


  protected function parameterCondition($value)
  {
    $criteria = new CDbCriteria();
    $criteria->compare('param_id', '='.$this->id);
    $criteria->addInCondition('variant_id', $this->groupValues[$value]);

    return $criteria;
  }
}