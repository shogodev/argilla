<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 *
 *  Пример:
 *  $criteria = new CDbCriteria();
 *  $criteria->compare('a.category_id', $model->id);
 *  $dataProviderList = new DataProviderList('ProductType', $criteria, 5);
 *
 *  $data = $dataProviderList->getDataProvider($modelId)
 */
class DataProviderList
{
  private $modelName;

  private $criteria;

  private $productsLimit;

  private $models;

  private $dataProviders;

  /**
   * @param string $modelName
   * @param CDbCriteria $criteria
   * @param integer $productsLimit
   */
  public function __construct($modelName, $criteria, $productsLimit = null)
  {
    $this->modelName = $modelName;
    $this->criteria = $criteria;
    $this->productsLimit = $productsLimit;
  }

  public function getModels()
  {
    if( is_null($this->models) )
    {
      $this->models = array();

      foreach(ProductAssignment::model()->getModels($this->modelName, $this->criteria) as $key => $model)
      {
        $this->models[$model->primaryKey] = $model;
      }
    }

    return $this->models;
  }

  public function getDataProvider($modelId)
  {
    if( is_null($this->dataProviders) )
    {
      $this->dataProviders = array();
      $commonProductIds = array();
      $modelProductIds = array();

      foreach($this->getModels() as $key => $model)
      {
        $this->models[$model->primaryKey] = $model;
        $field = ProductAssignment::getFieldByModel($this->modelName);

        $criteria = clone $this->criteria;
        $criteria->limit = $this->productsLimit;
        $criteria->compare('a.'.$field, $model->primaryKey);

        $modelProductIds[$model->primaryKey] = $this->getProductIds($criteria);
        $commonProductIds = CMap::mergeArray($commonProductIds, $modelProductIds[$model->primaryKey]);
      }

      $productsCriteria = new CDbCriteria();
      $productsCriteria->addInCondition('t.id', $commonProductIds);
      $productList = new ProductList($productsCriteria, null, false);

      $groupedProducts = array();
      foreach($productList->getDataProvider()->getData() as $product)
      {
        foreach($modelProductIds as $modelId => $productIds)
        {
          if( array_search($product->id, $productIds) !== false )
            $groupedProducts[$modelId][$product->id] = $product;
        }
      }

      foreach($groupedProducts as $modelId => $products)
      {
        $this->dataProviders[$modelId] = new FArrayDataProvider($products, array('pagination' => false));
      }
    }

    return isset($this->dataProviders[$modelId]) ? $this->dataProviders[$modelId] : null;
  }

  private function getProductIds(CDbCriteria $criteria)
  {
    $criteria->select = 't.id';
    $criteria->compare('t.visible', 1);

    ProductAssignment::model()->addAssignmentCondition($criteria);

    $builder = new CDbCommandBuilder(Yii::app()->db->getSchema());
    $command = $builder->createFindCommand(Product::model()->tableName(), $criteria);

    return $command->queryColumn();
  }
}