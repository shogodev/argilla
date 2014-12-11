<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.product.behaviors
 *
 * Поведение для работы с похожими и рекомендованными товарами
 *
 * Пример:
 *
 *  public function behaviors()
 *  {
 *    return array(
 *    ...
 *    'relatedProductsBehavior' => array(
 *       'class' => 'RelatedProductsBehavior',
 *        'groupRelatedThrough' => 'type'
 *    ),
 *    ...
 *    );
 *  }
 *
 * @property Product $owner
 */
class RelatedProductsBehavior extends AssociationBehavior
{
  public $groupRelatedThrough = null;

  /**
   * @param int $limit
   *
   * @return FActiveDataProvider
   */
  public function getRelatedProducts($limit = 5)
  {
    if( !$relatedProductIds = $this->getAssociationForMe('Product')->getKeys() )
    {
      $relatedProductIds = $this->getRelatedGroupProducts();
    }

    $criteria = new CDbCriteria();
    $criteria->addInCondition('t.id', array_slice($relatedProductIds, 0, $limit));

    return $this->getProductList($criteria)->getDataProvider();
  }

  /**
   * Возвращает продукты, ближайшие по цене к данному
   *
   * @param int $limit
   *
   * @return FActiveDataProvider
   */
  public function getSimilarProducts($limit = 5)
  {
    if( $this->owner->isNewRecord )
      return false;

    $criteria = new CDbCriteria();
    $criteria->order = 'ABS(price - :price)';
    $criteria->limit = $limit;
    $criteria->params[':price'] = $this->owner->price;

    if( isset($this->owner->section) )
      $criteria->compare('a.section_id', $this->owner->section->id);

    return $this->getProductList($criteria)->getDataProvider();
  }

  /**
   * @param CDbCriteria $criteria
   *
   * @return ProductList
   *
   */
  private function getProductList(CDbCriteria $criteria)
  {
    $criteria->compare('t.dump', 1);
    $criteria->compare('t.id', '<>'.$this->owner->id);

    return new ProductList($criteria, null, false);
  }

  private function getRelatedGroupProducts()
  {
    $relatedProducts = array();

    try
    {
      Yii::import('backend.modules.productGroup.frontendModels.ProductGroup', true);
      $associationIds = $this->getAssociationForMe('ProductGroup')->getKeys();

      if( empty($associationIds) && !is_null($this->groupRelatedThrough) )
        $associationIds = $this->owner->{$this->groupRelatedThrough}->getAssociationForMe('ProductGroup')->getKeys();

      /**
       * @var ProductGroup $group
       */
      foreach(ProductGroup::model()->findAllByPk($associationIds) as $group)
        $relatedProducts = CMap::mergeArray($relatedProducts, $group->getAssociationForMe('Product')->getKeys());
    }
    catch(CException $exception)
    {
    }

    return $relatedProducts;
  }
}