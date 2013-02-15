<?php
/**
 * @property string $name
 */
abstract class ProductFilterElement extends CComponent
{
  public $id;

  public $key;

  public $type;

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

  abstract public function addPropertyCondition(CDbCriteria $criteria);

  abstract public function addParameterCondition(CDbCriteria $criteria);

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

    return in_array($this->selected, $selected);
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
      if( (is_array($this->parent->state[$this->id]) && in_array($this->parent->state[$this->id], $itemId)) || $this->parent->state[$this->id] == $itemId )
        return true;
    }

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

  public function setSelected($state, $elementAvailableValues)
  {
    $this->selected = isset($state[$this->id]) ? $state[$this->id] : array();
  }

  public function prepareAvailableValues($value, $filtered)
  {
    return $value;
  }

  public function jsonSerialize()
  {
    $items = array();

    if( isset($this->items) )
    {
      foreach($this->items as $item)
      {
        $items[$item->id] = array(
          'name'     => $item->getName(),
          'disabled' => $item->isDisabled() ? 'true' : 'false'
        );
      }
    }

    return array(
      'name'  => $this->name,
      'type'  => $this->type,
      'items' => $items,
    );
  }

  protected function sortItems($items)
  {
    if( empty($items) )
      return $items;

    uasort($items, function($a, $b){
      return strnatcmp($a->label, $b->label);
    });

    return $items;
  }
}