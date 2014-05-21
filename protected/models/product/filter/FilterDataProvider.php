<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.product.filter
 */
class FilterDataProvider
{
  /**
   * @var CDbCommandBuilder
   */
  private $builder;

  /**
   * @var CDbCriteria
   */
  private $facetedCriteria;

  /**
   * @var FacetedDataProcessor
   */
  private $amountCounter;

  /**
   * @var array
   */
  private $filteredIds;

  /**
   * @var array
   */
  private $amounts;

  /**
   * @param FilterState $state
   * @param CDbCriteria $actionCriteria
   * @param FilterElement[] $elements
   */
  public function __construct(FilterState $state, CDbCriteria $actionCriteria, array $elements)
  {
    $this->builder = new CDbCommandBuilder(Yii::app()->db->getSchema());
    $this->amountCounter = new FacetedDataProcessor(new FilterProcessor($state, $elements));

    $this->setFacetedCriteria($actionCriteria);
  }

  /**
   * @return CDbCriteria
   */
  public function getFilteredCriteria()
  {
    $this->fetchData();

    $criteria = new CDbCriteria();
    $criteria->addInCondition('id', $this->filteredIds);

    return $criteria;
  }

  /**
   * @return array
   */
  public function getAmounts()
  {
    $this->fetchData();

    return $this->amounts;
  }

  private function fetchData()
  {
    if( $this->amounts === null && !$this->initFromCache() )
    {
      foreach($this->getFacetedData() as $data)
      {
        $this->amountCounter->prepare($data['param_id'], $data['value'], $data['product_id']);
      }

      $this->filteredIds = $this->amountCounter->getFilteredIds();
      $this->amounts = $this->amountCounter->getAmounts();

      $this->setCache();
    }
  }

  /**
   * @param $actionCriteria
   */
  private function setFacetedCriteria(CDbCriteria $actionCriteria)
  {
    $productIds = $this->getProductsByActionCriteria($actionCriteria);

    $this->facetedCriteria = new CDbCriteria();
    $this->facetedCriteria->distinct = true;
    $this->facetedCriteria->select = 'product_id, param_id, value';
    $this->facetedCriteria->addInCondition('product_id', $productIds);
  }

  /**
   * @return array
   */
  private function getFacetedData()
  {
    $command = $this->builder->createFindCommand(FacetedSearch::model()->tableName(), $this->facetedCriteria);

    return $command->queryAll();
  }

  /**
   * @param CDbCriteria $criteria
   *
   * @return array
   */
  private function getProductsByActionCriteria(CDbCriteria $criteria)
  {
    $criteria->select = 't.id';
    $command = $this->builder->createFindCommand(Product::model()->tableName(), $criteria);

    return $command->queryColumn();
  }

  /**
   * @return bool
   */
  private function initFromCache()
  {
    $this->filteredIds = array();
    $this->amounts = array();

    return false;
  }

  private function setCache()
  {
    $cache = array($this->filteredIds, $this->amounts);
  }
}