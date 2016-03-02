<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.xml
 */
Yii::import('ext.retailcrm.components.product.RetailCrmProductList');

/**
 * Class RetailCrmDataProvider
 */
class RetailCrmDataProvider
{
  public $catalogStructure = array('section');

  protected $productCounter;

  protected $offersIterator;

  public function __construct(CDbCriteria $criteria)
  {
    $this->criteria = $criteria;
  }

  /**
   * @return array
   */
  public function getShop()
  {
    return array(
      'name' => 'relax-market.ru',
      'company' => 'Relax market',
      'url' => 'http://www.relax-market.ru/'
    );
  }

  /**
   * @return array
   */
  public function getCategories()
  {
    $criteria = new CDbCriteria();
    $criteria->addCondition('product.parent IS NULL OR product.parent=0');
    $criteria->mergeWith($this->criteria);

    $baseStructureElement = Arr::get($this->catalogStructure, 0);

    $categories = array();
    foreach(ProductAssignment::model()->getAssignments($criteria) as $assignment)
    {
      if( empty($assignment[$baseStructureElement.'_id']) )
        continue;

      $idElements = array();
      foreach($this->catalogStructure as $structureElement)
      {
        $idIndex = $structureElement.'_id';
        $nameIndex = $structureElement.'_name';

        if( !empty($assignment[$idIndex]) )
        {
          $idElements[] = $assignment[$idIndex];
          $id = implode('_', $idElements);

          if( !isset($categories[$id]) )
          {
            $categories[$id] = array(
              'id' => $id,
              'name' => XmlHelper::escape($assignment[$nameIndex])
            );

            $parentIdElements = $idElements;
            array_pop($parentIdElements);
            if( !empty($parentIdElements) )
            {
              $categories[$id]['parent'] = $id = implode('_', $parentIdElements);
            }
          }
        }
      }
    }

    return $categories;
  }

  public function getOffers()
  {
    if( is_null($this->offersIterator) )
    {
      $productList = $this->getProductList();
      $this->offersIterator = new OfferIterator($productList->getDataProvider(), 100);
      $this->offersIterator->productList = $productList;
      $this->offersIterator->buildCategoryCallback = array($this, 'buildCategory');
    }

    return $this->offersIterator;
  }

  public function buildCategory(Product $product)
  {
    $idElements = array();
    foreach($this->catalogStructure as $structureElement)
    {
      if( !empty($product->{$structureElement}) )
        $idElements[] = $product->{$structureElement}->id;
    }

    $categoryIds = array(implode('_', $idElements));

    return $categoryIds;
  }

  /**
   * @return RetailCrmProductList
   */
  protected function getProductList()
  {
    $criteria = new CDbCriteria();
    $criteria->mergeWith($this->criteria);

    $productList = new ProductList($criteria, null, false, null);

    return $productList;
  }
}