<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>, Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.product.filter
 *
 * @property string $name
 */
abstract class ProductFilterElement extends CComponent
{
  const MERGE_TYPE_NONE = '';

  const MERGE_TYPE_OR = 'OR';

  const MERGE_TYPE_AND = 'AND';

  public $id;

  public $key;

  public $type;

  public $mergeType = self::MERGE_TYPE_NONE;

  public $label;

  public $notice;

  public $selected;

  public $disabled = array();

  /**
   * @var ProductFilterElementItem[] $items
   */
  public $items = array();

  public $itemLabels = array();

  /**
   * @var ProductFilter
   */
  protected $parent;

  /**
   * @param $availableValues
   *
   * @return bool
   */
  public function inAvailableValues($availableValues)
  {
    if( !isset($availableValues[$this->id]) )
      return false;

    $selected = $availableValues[$this->id];

    if( !is_array($selected) )
      $selected = array($selected);

    if( !$this->isMultiple() )
      return in_array($this->selected, $selected);

    if( !empty($this->selected) )
      $intersect = array_intersect_key($this->selected, $selected);

    return !empty($intersect);
  }

  public function addPropertyConditions(CDbCriteria $criteria)
  {
    $propertyCriteria = array();

    if( !$this->isSelected() )
      return;

    $selected = is_array($this->selected) ? $this->selected : array($this->selected);

    foreach($selected as $value)
      $propertyCriteria[] = $this->propertyCondition($value);

    $criteria->mergeWith($this->mergeElementsCriteria($propertyCriteria));
  }

  public function getParameterConditions()
  {
    $parameterCriteria = array();

    if( !$this->isSelected() )
      return null;

    $selected = is_array($this->selected) ? $this->selected : array($this->selected);

    foreach($selected as $value)
      $parameterCriteria[] = $this->parameterCondition($value);

    return $this->mergeElementsCriteria($parameterCriteria);
  }

  public function setParent($parent)
  {
    $this->parent = $parent;
  }

  public function getParent()
  {
    return $this->parent;
  }

  public function getName()
  {
    return $this->parent->filterKey.'['.$this->id.']';
  }

  /**
   * @return array
   */
  public function getDisabled()
  {
    $disabled = array();
    foreach($this->disabled as $key)
      $disabled[$key] = array('disabled' => 'disabled');

    return $disabled;
  }

  /**
 * @param $itemId
 *
 * @return bool
 */
  public function isSelectedItems($itemId)
  {
    if( isset($this->parent->state[$this->id]) )
    {
      if( !$this->isMultiple() )
      {
        if( (is_array($this->parent->state[$this->id]) && in_array($this->parent->state[$this->id], $itemId)) )
          return true;

        if( $this->parent->state[$this->id] == $itemId )
          return true;
      }

      if( isset($this->parent->state[$this->id][$itemId]) )
        return true;
    }

    return false;
  }

  public function isSelected()
  {
    foreach($this->items as $item)
      if( $item->isSelected() )
        return true;

    return false;
  }

  public function isParameter()
  {
    return is_numeric($this->id);
  }

  public function isProperty()
  {
    return !$this->isParameter();
  }

  public function buildItems($items)
  {
    $newItems = array();

    foreach($items as $itemId => $item)
    {
      $newItems[$itemId] = Yii::createComponent(
        array(
          'id' => $itemId,
          'class' => 'ProductFilterElementItem',
          'parent' => $this,
        )
      );
    }

    if( !empty($newItems) )
      $this->items = CMap::mergeArray($this->items, $this->sortItems($newItems));
  }

  public function setSelected($state)
  {
    $this->selected = isset($state[$this->id]) ? $state[$this->id] : array();
  }

  public function prepareAvailableValues($value)
  {
    return $value;
  }

  /**
   * @param CDbCriteria $criteria
   * @return CDbCriteria
   */
  public function buildPropertyAmountCriteria(CDbCriteria $criteria)
  {
    $criteria->distinct = true;
    $criteria->select = $this->id.', COUNT(t.id) AS count';
    $criteria->group  = $this->id;

    return $criteria;
  }

  public function isMultiple()
  {
    return $this->mergeType == self::MERGE_TYPE_NONE ? false : true;
  }

  protected function sortItems($items)
  {
    if( empty($items) )
      return $items;

    if( !empty($this->itemLabels) )
    {
      $sortedItems = array();
      foreach($this->itemLabels as $key => $label)
        if( isset($items[$key]) )
          $sortedItems[$key] = $items[$key];

      $items = $sortedItems;
    }
    else
    {
      uasort($items, function($a, $b){
        return strnatcmp($a->label, $b->label);
      });
    }

    return $items;
  }

  protected function mergeElementsCriteria($criteria)
  {
    $mergedCriterion = new CDbCriteria();

    foreach($criteria as $criterion)
      $mergedCriterion->mergeWith($criterion, $this->mergeType);

    return $mergedCriterion;
  }

  protected function propertyCondition($value)
  {
    $criteria = new CDbCriteria();
    $criteria->compare($this->id, $value);

    return $criteria;
  }

  protected function parameterCondition($value)
  {
    $criteria = new CDbCriteria();
    $criteria->compare('param_id', $this->id);
    $criteria->compare('variant_id', $value);

    return $criteria;
  }
}