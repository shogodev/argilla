<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.product.filter
 */
class FacetedDataProcessor
{
  /**
   * @var array
   */
  private $amounts;

  /**
   * @var array
   */
  private $facetedData = array();

  /**
   * @var array
   */
  private $totalIds = array();

  /**
   * @var FilterProcessor
   */
  private $processor;

  public function __construct(FilterProcessor $processor)
  {
    $this->processor = $processor;
  }

  /**
   * @param integer $elementId
   * @param integer $itemId
   * @param $productId
   *
   * @return integer
   */
  public function prepare($elementId, $itemId, $productId)
  {
    $itemId = $this->processor->prepare($elementId, $itemId, $productId);
    $this->increaseAmount($elementId, $itemId);

    $this->totalIds[] = $productId;
    $this->facetedData[$elementId][$itemId][] = $productId;
  }

  /**
   * @return array
   */
  public function getFilteredIds()
  {
    if( $this->processor->isStateEmpty() )
      return $this->totalIds;

    $this->process();

    return $this->processor->getSelectedIds();
  }

  /**
   * @return array
   */
  public function getAmounts()
  {
    return $this->amounts;
  }

  private function process()
  {
    $this->processor->init();

    foreach($this->facetedData as $elementId => $items)
    {
      $this->processor->setSelectedMask($elementId);

      foreach($items as $itemId => $productIds)
      {
        $amount = $this->processor->getAmount($elementId, $itemId, $productIds);
        $this->setAmount($elementId, $itemId, $amount);
      }
    }
  }

  /**
   * @param $elementId
   * @param $itemId
   */
  private function increaseAmount($elementId, $itemId)
  {
    if( !isset($this->amounts[$elementId]) )
      $this->amounts[$elementId] = array();

    if( !isset($this->amounts[$elementId][$itemId]) )
      $this->amounts[$elementId][$itemId] = 0;

    $this->amounts[$elementId][$itemId]++;
  }

  /**
   * @param string $elementId
   * @param string $itemId
   * @param integer $amount
   */
  private function setAmount($elementId, $itemId, $amount)
  {
    if( isset($this->amounts[$elementId][$itemId]) )
    {
      $this->amounts[$elementId][$itemId] = $amount;
    }
  }
}