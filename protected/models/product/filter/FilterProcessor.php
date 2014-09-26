<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package
 */
class FilterProcessor extends CComponent
{
  /**
   * @var FilterState
   */
  private $state;

  /**
   * @var FilterElement[]
   */
  private $elements;

  /**
   * @var array
   */
  private $selectedData = array();

  /**
   * @var array
   */
  private $selectedIds;

  /**
   * @var array
   */
  private $mask;

  /**
   * @param FilterState $state
   * @param FilterElement[] $elements
   */
  public function __construct(FilterState $state, array $elements)
  {
    $this->state = $state;
    $this->elements = $elements;
  }

  public function getSelectedIds()
  {
    return array_unique($this->selectedIds);
  }

  public function isStateEmpty()
  {
    return !$this->state->isSelected();
  }

  public function init()
  {
    foreach($this->getMergedSelectedData() as $values)
    {
      $selectedIds = !isset($selectedIds) ? $values : array_intersect($selectedIds, $values);
    }

    $this->selectedIds = !isset($selectedIds) ? array() : $selectedIds;
  }

  /**
   * @param string $elementId
   * @param string $itemId
   * @param integer $productId
   *
   * @return mixed|string
   */
  public function prepare($elementId, $itemId, $productId)
  {
    $itemId = $this->prepareValue($elementId, $itemId);
    $this->collectSelectedData($elementId, $itemId, $productId);

    return $itemId;
  }

  /**
   * @param string $elementId
   *
   * @return array
   */
  public function setSelectedMask($elementId)
  {
    $selectedDataMask = $this->selectedData;
    unset($selectedDataMask[$elementId]);

    $this->mask = $selectedDataMask;
  }

  /**
   * @param $elementId
   * @param $itemId
   * @param array $productIds
   *
   * @return int
   */
  public function getAmount($elementId, $itemId, $productIds)
  {
    $amount = 0;
    $mergedMask = $this->getMergedMask($elementId, $itemId, $productIds);

    if( $mergedMask !== null )
    {
      if( !$this->isSelected($elementId) || $this->isSelected($elementId, $itemId) )
      {
        $notCountedIds = $this->selectedIds;
      }
      else
      {
        $notCountedIds = $mergedMask;
      }

      if( !empty($notCountedIds) )
      {
        $amount = count(array_intersect($productIds, $notCountedIds));
      }
      else
      {
        $amount = count($productIds);
      }
    }

    return $amount;
  }

  /**
   * @param $elementId
   * @param $itemId
   * @param $productId
   */
  private function collectSelectedData($elementId, $itemId, $productId)
  {
    if( !$this->isSelected($elementId) && $this->state->isSelected($elementId) )
    {
      $this->selectedData[$elementId] = array();
    }

    if( $this->state->isSelected($elementId, $itemId) )
    {
      $this->selectedData[$elementId][$itemId][] = $productId;
    }
  }

  /**
   * @param $elementId
   * @param null $itemId
   *
   * @return bool
   */
  private function isSelected($elementId, $itemId = null)
  {
    if( !isset($itemId) )
    {
      return isset($this->selectedData[$elementId]);
    }
    else
    {
      return isset($this->selectedData[$elementId][$itemId]);
    }
  }

  /**
   * @param $elementId
   * @param $itemId
   * @param array $productIds
   *
   * @return array
   */
  private function getMergedMask($elementId, $itemId, $productIds)
  {
    foreach($this->mask as $selectedItems)
    {
      $merged = array();

      foreach($selectedItems as $selectedProductIds)
      {
        if( $intersection = array_intersect($productIds, $selectedProductIds) )
        {
          $merged = array_merge($merged, $intersection);
        }
        else
        {
          $this->correctSelectedIds($elementId, $itemId, $productIds);
          return null;
        }
      }
    }

    return isset($merged) ? $merged : array();
  }

  /**
   * @param $elementId
   * @param $itemId
   * @param array $productIds
   */
  private function correctSelectedIds($elementId, $itemId, $productIds)
  {
    if( $this->isSelected($elementId, $itemId) )
    {
      $this->selectedIds = array_diff($this->selectedIds, $productIds);
    }
  }

  /**
   * @return array
   */
  private function getMergedSelectedData()
  {
    $mergedSelectedData = array();

    foreach($this->selectedData as $elementId => $values)
    {
      if( $element = Arr::get($this->elements, $elementId) )
      {
        $selectedIds = array();

        foreach($values as $ids)
        {
          $selectedIds = $element->mergeSelected($selectedIds, $ids);
        }

        $mergedSelectedData[$elementId] = $selectedIds;
      }
    }

    return $mergedSelectedData;
  }

  /**
   * @param $elementId
   * @param $value
   *
   * @return mixed
   */
  private function prepareValue($elementId, $value)
  {
    if( isset($this->elements[$elementId]) )
    {
      $value = $this->elements[$elementId]->prepareAvailableValue($value);
    }

    return $value;
  }
}