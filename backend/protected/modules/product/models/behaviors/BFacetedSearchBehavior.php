<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.product.behaviors
 */
Yii::import('backend.modules.product.components.FacetIndexer');
/**
 * Class BFacetedSearchBehavior
 *
 * * @property BProduct $owner
 */
class BFacetedSearchBehavior extends SActiveRecordBehavior
{
  /**
   * @var FacetIndexer $facetIndexer
   */
  protected $facetIndexer;

  public function init()
  {
    parent::init();

    $this->facetIndexer = new FacetIndexer();
  }

  public function afterSave($event)
  {
    /**
     * BProduct $product
     */
    $product = $this->owner;

    if( $this->owner->asa('modificationBehavior') )
    {
      if( $parentId = $this->owner->getParentId() )
        $this->facetIndexer->clearIndexByProductIdList(array($parentId));

      $productIdList = $this->owner->getFacetProductIdList();
    }
    else
      $productIdList = array($product->id);

    $this->facetIndexer->reindexProducts($productIdList);
  }
}