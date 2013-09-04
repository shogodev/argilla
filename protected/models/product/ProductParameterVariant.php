<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.product
 *
 * @property string $id
 * @property string $param_id
 * @property string $name
 * @property string $position
 *
 * @property ProductParameterName $param
 *
 * @method static ProductParameterVariant model(string $class = __CLASS__)
 */
class ProductParameterVariant extends FActiveRecord
{
  public function tableName()
  {
    return '{{product_param_variant}}';
  }

  public function __toString()
  {
    return $this->name;
  }

  /**
   * @param ProductParameterName[] $parameterNames
   */
  public function setVariants(array $parameterNames)
  {
    $variants   = array();
    $variantIds = array();

    foreach($parameterNames as $name)
    {
      $variantIds = CMap::mergeArray($variantIds, $name->getVariantKeys());
    }

    foreach($this->findAllByPk($variantIds) as $variant)
    {
      $variants[$variant['id']] = $variant;
    }

    foreach($parameterNames as $parameter)
    {
      $parameter->setVariants($variants);
    }
  }
}