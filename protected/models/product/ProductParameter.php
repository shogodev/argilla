<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.product
 *
 * @property int $param_id
 * @property int $product_id
 * @property int $variant_id
 * @property string $value
 *
 * @method static ProductParameter model(string $class = __CLASS__)
 */
class ProductParameter extends FActiveRecord
{
  public function tableName()
  {
    return '{{product_param}}';
  }

  /**
   * @param ProductParameterName[] $parameterNames
   */
  public function setParameterValues(array $parameterNames)
  {
    $productIds = CHtml::listData($parameterNames, 'id', 'productId');

    if( !empty($productIds) )
    {
      $criteria = new CDbCriteria();
      $criteria->addInCondition('product_id', array_unique($productIds));
      $criteria->addInCondition('param_id', array_keys($productIds));

      $parameters = $this->findAll($criteria);
      $parameters = $this->rearrangeParameters($parameters);

      foreach($parameterNames as $parameter)
      {
        if( isset($parameters[$parameter->productId][$parameter->id]) )
        {
          $parameter->setValues($parameters[$parameter->productId][$parameter->id]);
        }
      }

      ProductParameterVariant::model()->setVariants($parameterNames);
    }
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