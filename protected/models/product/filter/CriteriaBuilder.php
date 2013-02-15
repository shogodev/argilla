<?php
/**
 * User: Sergey Glagolev <glagolev@shogo.ru>
 * Date: 10.12.12
 */
class CriteriaBuilder
{
  /**
   * @var $propertyCriteria CDbCriteria
   */
  protected $propertyCriteria;

  /**
   * @var $parametersCriteria CDbCriteria
   */
  protected $parametersCriteria;

  public function __construct($actionCriteria)
  {
    $this->propertyCriteria   = clone $actionCriteria;
    $this->parametersCriteria = new CDbCriteria();
  }

  public function addCondition(ProductFilterElement $element)
  {
    if( $element->isProperty() )
      $element->addPropertyCondition($this->propertyCriteria);
    else
      $element->addParameterCondition($this->parametersCriteria);
  }

  public function getFilteredCriteria()
  {
    $this->mergeParamCriteria($this->propertyCriteria, $this->parametersCriteria);

    return $this->propertyCriteria;
  }

  public function getAvailableValuesCriteria(array $elements)
  {
    $properties = array();
    foreach($elements as $element)
      if( $element->isProperty() )
        $properties[] = $element->id;

    $criteria = $this->propertyCriteria;
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

  protected function mergeParamCriteria(CDbCriteria $propertyCriteria, CDbCriteria $paramCriteria)
  {
    if( !empty($paramCriteria->condition) )
    {
      $paramCriteria->select = 'product_id, count( param_id ) AS count';
      $paramCriteria->group  = 'product_id';
      $paramCriteria->having = 'count = '.substr_count($paramCriteria->condition, 'param_id');

      $builder    = new CDbCommandBuilder(Yii::app()->db->getSchema());
      $command    = $builder->createFindCommand(ProductParam::model()->tableName(), $paramCriteria);
      $productIds = $command->queryColumn();

      $propertyCriteria->addInCondition('t.id', $productIds);
    }
  }
}