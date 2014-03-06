<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.product
 *
 * @property integer $id
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

  public function defaultScope()
  {
    return array(
      'order' => 'IF(position=0, 1, 0), position ASC, name'
    );
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
    $variantIds = array();
    foreach($parameterNames as $name)
      $variantIds = CMap::mergeArray($variantIds, $name->getVariantKeys());

    $variants = $this->findAllByAttributes(array('id' => $variantIds), new CDbCriteria(array('index' => 'id')));

    foreach($parameterNames as $parameterName)
    {
      $parameterName->setVariants($variants);
      $this->sortParameters($parameterName);
    }
  }

  protected function sortParameters(ProductParameterName $parameterName)
  {
    $sortedParameters = array();

    $sortedIndexes = array();
    foreach($parameterName->values as $index => $variant)
    {
      if( isset($parameterName->parameters[$index]) )
      {
        $sortedParameters[$index] = $parameterName->parameters[$index];
        $sortedIndexes[] = $index;
      }
    }

    foreach($parameterName->parameters as $index => $parameter)
      if( !in_array($index, $sortedIndexes) )
        $sortedParameters[$index] = $parameterName->parameters[$index];

    $parameterName->parameters = $sortedParameters;
  }
}