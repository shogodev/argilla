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
 * @property Product $owner
 */
class RelatedProductsBehavior extends CModelBehavior
{
  /**
   * @param int $limit
   *
   * @return Product[]
   */
  public function getRelatedProducts($limit = 5)
  {
    if( !$relatedProducts = $this->owner->findAllThroughAssociation(new Product()) )
    {
      $associationIds = $this->owner->findAllThroughAssociation(new ProductGroup());

      if( empty($associationIds) )
        $associationIds = $this->owner->section->findAllThroughAssociation(new ProductGroup());

      /**
       * @var ProductGroup $group
       */
      foreach(ProductGroup::model()->findAllByPk($associationIds) as $group)
        $relatedProducts = CMap::mergeArray($relatedProducts, $group->findAllThroughAssociation(new Product()));
    }

    $criteria = new CDbCriteria();
    $criteria->addInCondition('t.id', array_slice($relatedProducts, 0, $limit));

    return $this->getProductList($criteria)->getDataProvider()->getData();
  }

  /**
   * Возвращает продукты, ближайшие по цене к данному
   *
   * @param int $limit
   *
   * @return Product[]
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

    return $this->getProductList($criteria)->getDataProvider()->getData();
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
}