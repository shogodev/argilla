<?php

/**
 * @property int $param_id
 * @property int $product_id
 * @property int $variant_id
 * @property string $value
 */
class ProductParam extends FActiveRecord
{
  public function tableName()
  {
    return '{{product_param}}';
  }

  /**
   * @param CDbCriteria|null $namesCriteria
   * @param bool $sortByGroups
   *
   * @return array
   */
  public function getParameters($namesCriteria = null, $sortByGroups = false)
  {
    $criteria = new CDbCriteria();
    $criteria->condition = 'visible=1';

    if( $namesCriteria !== null )
      $criteria->mergeWith($namesCriteria);

    $keys = array();
    if( isset($this->product_id) )
      $keys[] = $this->product_id;

    $parameters = array();
    $params     = array();
    $names      = ProductParamName::model()->search($criteria);

    $data = Yii::app()->db->createCommand()
      ->from(ProductParam::model()->tableName())
      ->where(array('AND', array('IN', 'product_id', $keys), array('IN', 'param_id', $names->getKeys())))
      ->query();

    foreach($data as $row)
      $params[$row['param_id']][$row['variant_id']] = $row;

    foreach($names->getData() as $name)
    {
      $paramName = clone $name;

      if( isset($params[$paramName->id]) )
        foreach($params[$paramName->id] as $value)
          $paramName->setValue($value);

      $parameters[] = $paramName;
    }

    return $sortByGroups ? $this->sortByGroups($parameters) : $parameters;
  }


  protected function sortByGroups($parameters)
  {
    $sorted = array();

    foreach($parameters as $param)
      $sorted[$param->group][] = $param;

    return $sorted;
  }
}