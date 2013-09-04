<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.product.filter
 *
 * @property integer $selectedMin;
 * @property integer $selectedMax;
 */
class ProductFilterElementRange extends ProductFilterElement
{
  public $round = true;

  public $borderRange = 0;

  protected $minValue = null;

  protected $maxValue = null;

  public function getSelectedMin()
  {
    if( !empty($this->selected) )
      $selected = $this->normalizeValue(explode("-", $this->selected)[0]);

    if( !isset($selected) )
      $selected = $this->minValue;

    return $selected;
  }

  public function getSelectedMax()
  {
    if( !empty($this->selected) )
      $selected = $this->normalizeValue(explode("-", $this->selected)[1]);

    if( !isset($selected) )
      $selected = $this->maxValue;

    return $selected;
  }

  public function prepareAvailableValues($value)
  {
    if( !empty($value) )
    {
      $value = $this->normalizeValue($value);

      $this->findMinMaxValue($value);
      $value = $this->getRangeAvailableValue($value);
    }

    return $value;
  }

  /**
   * @param CDbCriteria $criteria
   * @return CDbCriteria
   */
  public function buildPropertyAmountCriteria(CDbCriteria $criteria)
  {
    $criteria->distinct = true;

    if( empty($this->itemLabels) )
      return $criteria;

    $select = "(CASE \n";

    $i = 0;
    foreach($this->itemLabels as $key => $value)
    {
      $min = explode("-", $key)[0];
      $max = explode("-", $key)[1];
      $select .= "WHEN {$this->id} >= :{$i}_min AND {$this->id} <= :{$i}_max THEN :{$i}_key\n";

      $criteria->params[":{$i}_min"] = $min;
      $criteria->params[":{$i}_max"] = $max;
      $criteria->params[":{$i}_key"] = $key;
      $i++;
    }

    $select .= "ELSE 0 END)";

    // Пока единственное рабочее решение, GROUP по {$this->id} работает не корректно
    $criteria->select = "{$select} AS :element_group \n, {$select} AS :element_id \n , COUNT(t.id) AS count";
    $criteria->group  = "{$this->id}_key_for_group";

    $criteria->params[':element_id']    = $this->id;
    $criteria->params[':element_group'] = $this->id.'_key_for_group';

    return $criteria;
  }

  public function getMinValue($range = 0)
  {
    $range = $range ? $range : $this->borderRange;

    return $this->minValue - ($range ? $this->minValue%$range : 0);
  }

  public function getMaxValue($range = 0)
  {
    $range = $range ? $range : $this->borderRange;

    return $this->maxValue + ($range ? $this->maxValue%$range : 0);
  }

  protected function propertyCondition($value)
  {
    $range = explode("-", $value);

    $criteria = new CDbCriteria();
    $criteria->addBetweenCondition($this->id, $range[0], $range[1]);

    return $criteria;
  }

  protected function parameterCondition($value)
  {
    $criteria = new CDbCriteria();
    $criteria->compare('param_id', '='.$this->id);

    $param = explode("-", $value);
    if( !empty($param) && !empty($param[0]) && !empty($param[1]) )
      $criteria->addBetweenCondition('(value + 0)', $param[0], $param[1]);
    else if( !empty($param[0]) )
      $criteria->compare('(value + 0)', '>='.$param[0]);
    else if( !empty($param[1]) )
      $criteria->compare('(value + 0)', '<='.$param[1]);

    return $criteria;
  }

  protected function getRangeAvailableValue($value)
  {
    $value = $this->normalizeValue($value);

    foreach($this->itemLabels as $id => $itemLabel)
    {
      if( $value >= explode("-", $id)[0] && $value <= explode("-", $id)[1] )
        return $id;
    }

    return $value;
  }

  protected function sortItems($items)
  {
    if( empty($items) )
      return $items;

    uasort($items, function($a, $b){
      return strnatcmp($a->id, $b->id);
    });

    return $items;
  }

  protected function findMinMaxValue($value)
  {
    if( $this->minValue === null )
      $this->minValue = $value;

    if( $this->maxValue === null )
      $this->maxValue = $value;

    $this->minValue = $this->minValue <= $value ? $this->minValue : $value;
    $this->maxValue = $this->maxValue >= $value ? $this->maxValue : $value;
  }

  protected function normalizeValue($value)
  {
    $value = trim($value);
    return $this->round ? intval($value) : floatval(str_replace(',', '.', $value));
  }
}