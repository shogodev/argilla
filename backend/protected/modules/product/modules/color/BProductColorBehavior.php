<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.product.behaviors
 *
 *  Подключение:
 *  ...
 *   'productColorBehavior' => array('class' => 'color.BProductColorBehavior')
 *  ...
 */


/**
 * Class BProductColorBehavior
 * @property BProduct $owner
 */
class BProductColorBehavior extends SActiveRecordBehavior
{
  public function init()
  {
    $this->owner->getMetaData()->addRelation('colorProduct', array(
        BActiveRecord::HAS_MANY, 'BProductColor', 'product_id', 'order' => 'IF(position, position, 999999999)')
    );
  }

  public function updateColorParameters()
  {
    $variants = $this->getColorVariants();
    $this->deleteColorParameters($variants);

    foreach($variants as $id)
    {
      $this->saveColorParameter($id);
    }
  }

  /**
   * @return integer
   */
  public function getColorParameterId()
  {
    return BColor::COLOR_PARAMETER_ID;
  }

  /**
   * @param array $variants
   */
  private function deleteColorParameters(array $variants)
  {
    $criteria = new CDbCriteria();
    $criteria->compare('param_id', $this->getColorParameterId());
    $criteria->compare('product_id', $this->owner->id);
    $criteria->addNotInCondition('variant_id', $variants);

    BProductParam::model()->deleteAll($criteria);
  }

  /**
   * @param integer $id
   */
  private function saveColorParameter($id)
  {
    $attributes = array(
      'param_id' => $this->getColorParameterId(),
      'product_id' => $this->owner->id,
      'variant_id' => $id,
    );

    if( !$parameter = BProductParam::model()->findByAttributes($attributes) )
    {
      $parameter = new BProductParam();
      $parameter->setAttributes($attributes);
      $parameter->save();
    }
  }

  /**
   * @return array
   */
  private function getColorVariants()
  {
    $productColors = Yii::app()->db->createCommand()->selectDistinct('color_id')
      ->from(BProductColor::model()->tableName())
      ->where('product_id=:id')->queryColumn(array(':id' => $this->owner->id));

    $colorVariants = Yii::app()->db->createCommand()->selectDistinct('variant_id')
      ->from(BColor::model()->tableName())
      ->where(array('AND', array('IN', 'id', $productColors), 'variant_id IS NOT NULL'))->queryColumn();

    return $colorVariants;
  }
}