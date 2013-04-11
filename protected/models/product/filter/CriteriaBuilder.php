<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.product.filter
 */
class CriteriaBuilder
{
  /**
   * @var CDbCriteria $propertyCriteria
   */
  protected $mainCriteria;

  /**
   * @var CDbCriteria[] $parametersCriteria
   */
  protected $parameterCriteria;

  public function __construct($actionCriteria)
  {
    $this->mainCriteria = clone $actionCriteria;
  }

  public function addCondition(ProductFilterElement $element)
  {
    if( $element->isProperty() )
      $element->addPropertyCondition($this->mainCriteria);
    else
      $this->parameterCriteria[$element->id] = $element->getParameterCondition();
  }

  public function getFilteredCriteria()
  {
    if( !empty($this->parameterCriteria) )
      $this->mainCriteria->addInCondition('t.id', $this->getProductIdsByParameterCriteria($this->parameterCriteria));

    return $this->mainCriteria;
  }

  public function getAvailableValuesCriteria(array $elements)
  {
    $properties = array();

    foreach($elements as $element)
      if( $element->isProperty() )
        $properties[] = $element->id;

    $criteria = $this->mainCriteria;
    $criteria->select = implode(',', CMap::mergeArray($properties, array('p.param_id', 'p.variant_id')));
    $criteria->compare('visible', '=1');

    $criteria->group   = $criteria->select;
    $criteria->select .= ', COUNT(t.id) AS count';

    $assignment = ProductAssignment::model()->tableName();
    $parameters = ProductParam::model()->tableName();

    $criteria->join  = 'JOIN '.$assignment.' AS a ON a.product_id = t.id';
    $criteria->join .= ' LEFT JOIN '.$parameters.' AS p ON p.product_id = t.id';

    $criteria->distinct = true;

    return $criteria;
  }

  /**
   * @param CDbCriteria[] $parameterCriteria
   */
  protected function getProductIdsByParameterCriteria($parameterCriteria)
  {
    $productIds = array();

    foreach($parameterCriteria as $criteria)
    {
      if( empty($criteria->condition) )
        continue;

      $criteria->distinct = true;
      $criteria->select = 'product_id';

      if( $result = $this->queryExecute($criteria) )
        $productIds = empty($productIds) ? $result : array_intersect($productIds, $result);
      else
        return null;
    }

    return $productIds;
  }

  protected function queryExecute(CDbCriteria $criteria)
  {
    $builder = new CDbCommandBuilder(Yii::app()->db->getSchema());
    $command = $builder->createFindCommand(ProductParam::model()->tableName(), $criteria);
    return $command->queryColumn();
  }
}