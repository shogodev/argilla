<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>, Alexey Tatarinov <tatarinov@shogo.ru>
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
      $element->addPropertyConditions($this->mainCriteria);
    else
    {
      $parameterCondition = $element->getParameterConditions();

      if( $parameterCondition )
        $this->parameterCriteria[$element->id] = $parameterCondition;
    }
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

    $criteria = clone $this->mainCriteria;
    $criteria->distinct = true;
    $criteria->select = implode(',', CMap::mergeArray($properties, array('p.param_id', 'p.variant_id', 'p.value')));
    $criteria->group  = $criteria->select;
    $criteria->compare('t.visible', 1);
    $criteria->compare('a.visible', 1);

    $assignment = ProductAssignment::model()->tableName();
    $parameters = ProductParameter::model()->tableName();

    $criteria->join  = 'JOIN '.$assignment.' AS a ON a.product_id = t.id';
    $criteria->join .= ' LEFT JOIN '.$parameters.' AS p ON p.product_id = t.id';

    return $criteria;
  }

  /**
   * @param ProductFilterElement $element
   * @return CDbCriteria
   */
  public function getPropertyAmountCriteria($element)
  {
    $criteria = $element->buildPropertyAmountCriteria(clone $this->mainCriteria);

    $assignment = ProductAssignment::model()->tableName();
    $criteria->join  = 'JOIN '.$assignment.' AS a ON a.product_id = t.id';

    return $criteria;
  }

  /**
   * @param $parameter
   * @return CDbCriteria
   */
  public function getParameterAmountCriteria()
  {
    $criteria = clone $this->mainCriteria;
    $criteria->distinct = true;
    $criteria->select = implode(',', array('p.param_id', 'p.variant_id', 'p.value'));
    $criteria->group  = $criteria->select;
    $criteria->select .= ', COUNT(t.id) AS count';

    $parameters = ProductParameter::model()->tableName();
    $criteria->join .= ' LEFT JOIN '.$parameters.' AS p ON p.product_id = t.id';

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

      if( $result = $this->getQueryColumn($criteria) )
        $productIds = empty($productIds) ? $result : array_intersect($productIds, $result);
      else
        return null;
    }

    return $productIds;
  }

  protected function getQueryColumn(CDbCriteria $criteria)
  {
    $builder = new CDbCommandBuilder(Yii::app()->db->getSchema());
    $command = $builder->createFindCommand(ProductParameter::model()->tableName(), $criteria);
    return $command->queryColumn();
  }
}