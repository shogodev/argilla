<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>, Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.product.filter
 *
 * @property string $name
 * @property array $htmlOptions
 * @property Filter $parent
 */
abstract class FilterElement extends CComponent
{
  const MERGE_TYPE_SINGLE = 'NULL';

  const MERGE_TYPE_MULTIPLY_OR = 'OR';

  const MERGE_TYPE_MULTIPLY_AND = 'AND';

  public $id;

  public $key;

  public $type;

  public $label;

  public $selected;

  public $disabled = array();

  public $itemClass = 'FilterElementItem';

  /**
   * @var FilterElementItem[] $items
   */
  public $items = array();

  /**
   * @var array
   */
  public $itemLabels = array();

  protected $htmlOptions = array();

  /**
   * @var Filter
   */
  protected $parent;

  public function init($parent)
  {

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
  public function isItemSelected($itemId)
  {
    return $this->parent->state->isSelected($this->id, $itemId);
  }

  public function isSelected()
  {
    return $this->parent->state->isSelected($this->id);
  }

  /**
   * @return FilterElementItem[]
   */
  public function getItems()
  {
    return array_filter($this->items, function(FilterElementItem $item){
      return !$item->isDisabled();
    });
  }

  public function getSelectedItems()
  {
    return array_filter($this->items, function(FilterElementItem $item){
      return $item->isSelected();
    });
  }

  public function buildItems($items)
  {
    $this->clear();

    foreach($items as $itemId => $amount)
    {
      $this->items[$itemId] = Yii::createComponent(
        array(
          'id' => $itemId,
          'class' => $this->itemClass,
          'parent' => $this,
          'amount' => $amount,
        )
      );
    }

    $this->items = $this->sortItems($this->items);
  }

  /**
   * @param FilterState $state
   */
  public function setSelected(FilterState $state)
  {
    $this->selected = $state->isSelected($this->id) ? $state->offsetGet($this->id) : array();
  }

  /**
   * @param $value
   *
   * @return $value
   */
  public function prepareAvailableValue($value)
  {
    return $value;
  }

  public function isMultiple()
  {
    return $this->getMergeType() == self::MERGE_TYPE_SINGLE ? false : true;
  }

  /**
   * @param array $selectedIds
   * @param array $ids
   *
   * @return array
   */
  public function mergeSelected($selectedIds, $ids)
  {
    return $this->getMergeType() === self::MERGE_TYPE_MULTIPLY_AND ? array_intersect($selectedIds, $ids) : array_merge($selectedIds, $ids);
  }

  public function clear()
  {
    $this->selected = null;
    $this->disabled = array();
    $this->items = array();
  }

  public function disableAll()
  {
    $this->disabled = array_flip(array_keys($this->items));
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

  protected function getHtmlOptions()
  {
    $options = $this->htmlOptions;
    if( !empty($this->itemUrls) )
    {
      $options['class']    = trim(Arr::get($options, 'class', '').' url-dependence');
      $options['data-key'] = $this->id;
    }

    return $options;
  }

  protected function setHtmlOptions($options)
  {
    $this->htmlOptions = $options;
  }

  /**
   * @return string
   */
  abstract protected function getMergeType();
}