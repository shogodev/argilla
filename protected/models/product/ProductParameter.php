<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.product
 *
 * @property int $param_id
 * @property int $product_id
 * @property int $variant_id
 * @property string $value
 *
 * @method static ProductParameter model(string $class = __CLASS__)
 *
 * @property ProductParameterName $parameterName
 * @property ProductParameterVariant $variant
 */
class ProductParameter extends FActiveRecord
{
  public function tableName()
  {
    return '{{product_param}}';
  }

  public function relations()
  {
    return array(
      'parameterName' => array(self::BELONGS_TO, 'ProductParameterName', 'param_id'),
      'variant' => array(self::BELONGS_TO, 'ProductParameterVariant', 'variant_id'),
    );
  }

  public function behaviors()
  {
    return array('collectionElement' => array('class' => 'FCollectionParameter'));
  }

  /**
   * @param ProductParameterName[] $parameterNames
   */
  public function setParameterValues(array $parameterNames)
  {
    list($productIds, $paramIds) = $this->getParameterIds($parameterNames);

    if( !empty($productIds) )
    {
      $criteria = new CDbCriteria();
      $criteria->addInCondition('product_id', $productIds);
      $criteria->addInCondition('param_id', $paramIds);

      $parameters = $this->findAll($criteria);
      $parameters = $this->rearrangeParameters($parameters);

      foreach($parameterNames as $parameter)
      {
        if( isset($parameters[$parameter->productId][$parameter->id]) )
        {
          $parameter->setParameters($parameters[$parameter->productId][$parameter->id]);
        }
      }

      ProductParameterVariant::model()->setVariants($parameterNames);
    }
  }

  /**
   * @param ProductParameterName[] $parameterNames
   *
   * @return array
   */
  protected function getParameterIds($parameterNames)
  {
    $paramIds = array();
    $productIds = array();

    foreach($parameterNames as $name)
    {
      $productIds[] = $name->productId;
      $paramIds[]   = $name->id;
    }

    return array(array_unique($productIds), array_unique($paramIds));
  }

  /**
   * @param ProductParameter[] $productParameters
   *
   * @return array
   */
  protected function rearrangeParameters(array $productParameters)
  {
    $parameters = array();

    foreach($productParameters as $parameter)
      $parameters[$parameter['product_id']][$parameter['param_id']][$parameter['variant_id']] = $parameter;

    return $parameters;
  }
}