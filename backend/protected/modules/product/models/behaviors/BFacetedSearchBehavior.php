<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.product.behaviors
 *
 * @property BProduct $owner
 */
class BFacetedSearchBehavior extends SActiveRecordBehavior
{
  public function afterSave($event)
  {
    $this->refreshAll($this->owner);
  }

  /**
   * @param BProduct $product
   *
   * @return void
   */
  private function refreshAll(BProduct $product)
  {
    $this->remove($product);
    $this->add($product);
  }

  /**
   * @param BProduct $product
   */
  private function add(BProduct $product)
  {
    /**
     * @var BFacetedParameter $parameters
     */
    $parameters = BFacetedParameter::model()->findAll();

    foreach($parameters as $parameter)
    {
      foreach($this->getValues($product, $parameter->parameter) as $value)
      {
        if( empty($value) )
          continue;

        $item = new BFacetedSearch();
        $item->value = $value;
        $item->param_id = $parameter->parameter;
        $item->product_id = $product->id;
        $item->save();
      }
    }
  }

  /**
   * @param BProduct $product
   * @param $parameterId
   *
   * @return array
   */
  private function getValues(BProduct $product, $parameterId)
  {
    return is_numeric($parameterId) ? $this->getParameterValues($product, $parameterId) : $this->getPropertyValues($product, $parameterId);
  }

  /**
   * @param $product
   * @param $id
   *
   * @return array
   */
  private function getPropertyValues(BProduct $product, $id)
  {
    $value = isset($product->{$id}) ? $product->{$id} : null;
    return is_array($value) ? $value : array($value);
  }

  /**
   * @param BProduct $product
   * @param integer $id
   *
   * @return array
   */
  private function getParameterValues(BProduct $product, $id)
  {
    /**
     * @var BProductParam $parameter
     */
    $parameters = BProductParam::model()->findAllByAttributes(array('param_id' => $id, 'product_id' => $product->id));

    $values = array_map(function(BProductParam $parameter){
      $variant = $parameter->variant;
      return $variant ? $parameter->variant_id : $parameter->value;
    }, $parameters);

    return $values;
  }

  /**
   * @param BProduct $product
   *
   * @return integer
   */
  private function remove(BProduct $product)
  {
    $criteria = new CDbCriteria();
    $criteria->compare('product_id', $product->id);

    return BFacetedSearch::model()->deleteAll($criteria);
  }
}