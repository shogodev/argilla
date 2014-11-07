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
class BParametersUpdaterBehavior extends SActiveRecordBehavior
{
  const PRICE_PARAMETER_KEY = 'price';

  /**
   * @var bool Параметр товара зависит от его типа
   */
  public $typeDepended = false;

  public function afterSave()
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
   *
   * @return integer
   */
  private function remove(BProduct $product)
  {
    $criteria = new CDbCriteria();
    $criteria->compare('product_id', $product->id);
    $criteria->compare('name.key', self::PRICE_PARAMETER_KEY);
    $criteria->join = 'JOIN '.BProductParamName::model()->tableName().' AS name ON name.id = param_id';

    return BProductParam::model()->deleteAll($criteria);
  }

  /**
   * @param BProduct $product
   */
  private function add(BProduct $product)
  {
    if( $variant = $this->getPriceVariant($product) )
    {
      $parameter = new BProductParam();
      $parameter->param_id = $variant->param_id;
      $parameter->product_id = $product->id;
      $parameter->variant_id = $variant->id;
      $parameter->save();
    }
  }

  /**
   * @param BProduct $product
   *
   * @return BProductParamVariant
   */
  private function getPriceVariant(BProduct $product)
  {
    $criteria = new CDbCriteria();
    $criteria->compare('name.key', self::PRICE_PARAMETER_KEY);
    $criteria->join  = 'JOIN '.BProductParamName::model()->tableName().' AS name ON name.id = param_id ';
    $criteria->join .= 'JOIN '.BProductParamAssignment::model()->tableName().' AS assignment ON assignment.param_id = name.parent';

    if( $this->typeDepended )
      $criteria->compare('assignment.type_id', $product->type_id);

    /**
     * @var BProductParamVariant[] $variants
     */
    $variants = BProductParamVariant::model()->findAll($criteria);

    foreach($variants as $variant)
    {
      $range = explode('-', $variant->notice);
      if( $product->price > $range[0] && $product->price < $range[1] )
        return $variant;
    }

    return null;
  }
}