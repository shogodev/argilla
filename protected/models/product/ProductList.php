<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.product
 *
 * @property CDbCriteria $criteria
 */
class ProductList extends BaseList
{
  public $parameterNameModel = 'ProductParameterName';

  public $parameterModel = 'ProductParameter';

  public static $sortingRange = array(
    'popular_up' => 'IF(t.position=0, 1, 0), t.position DESC, dump DESC, IF(price=0, 1, 0)',
    'popular_down' => 'IF(t.position=0, 1, 0), t.position ASC, dump DESC, IF(price=0, 1, 0)',
    'price_up' => 'IF(price=0, 1, 0), price ASC',
    'price_down' => 'IF(price=0, 1, 0), price DESC',
    'name_up' => 'name ASC',
    'name_down' => 'name DESC',
    'available_up' => 'dump DESC',
    'available_down' => 'dump ASC',
  );

  /**
   * @var CDbCriteria
   */
  public $parametersCriteria;

  public $autoloadModifications = false;

  public function init()
  {
    $this->criteria->compare('t.visible', 1);
    $this->criteria->addCondition('t.parent IS NULL');
    ProductAssignment::model()->addAssignmentCondition($this->criteria);

    $prefix = $this->getTablePrefix();
    $this->parametersCriteria = new CDbCriteria();
    $this->parametersCriteria->addColumnCondition(array($prefix.'.section' => 1, $prefix.'.section_list' => 1, $prefix.'.key' => ProductParameter::BASKET_KEY), 'OR');
  }

  protected function afterFetchData($event)
  {
    $this->setImages();
    $this->setParameters();
    $this->setAssignments();
    if( $this->autoloadModifications )
      $this->setModifications();
  }

  protected function setParameters()
  {
    $criteria = new CDbCriteria();
    $criteria->select = 'param_id';
    $criteria->distinct = true;
    $criteria->addInCondition('product_id', $this->dataProvider->getKeys());

    $command = Yii::app()->db->commandBuilder->createFindCommand(ProductParameter::model()->tableName(), $criteria);
    $usedProductParameterIds = $command->queryColumn();

    $this->parametersCriteria->addInCondition('t.id', $usedProductParameterIds);

    $modelName = $this->parameterNameModel;
    $names = $modelName::model()->search($this->parametersCriteria);
    $parameters = array();

    /**
     * @var $products Product[]
     */
    $products = $this->getDataProvider()->getData();
    foreach($products as $product)
    {
      $product->setParameters();

      foreach($names as $name)
      {
        $productParameterName = clone $name;
        $productParameterName->setProductId($product->id);
        $product->addParameter($productParameterName);
        $parameters[] = $productParameterName;
      }
    }

    $modelName = $this->parameterModel;
    $modelName::model()->setParameterValues($parameters);
  }

  protected function setAssignments()
  {
    $assignments = array('section', 'type', 'category', 'collection');
    $criteria = new CDbCriteria(array('select' => 'a.product_id'));
    $criteria->addInCondition('product_id', $this->dataProvider->getKeys(true));
    $productAssignments = ProductAssignment::model()->getAssignments($criteria);

    foreach($assignments as $assignment)
    {
      $this->setAssignment($productAssignments, $assignment);
    }
  }

  protected function setAssignment($productAssignments, $modelName)
  {
    $models = array();
    $assignments = CHtml::listData($productAssignments, 'product_id', $modelName.'_id');
    $keys = array_unique(array_values($assignments));
    $records = $this->findRecords('id', 'Product'.ucfirst($modelName), $keys, new CDbCriteria(array('index' => 'id')));

    foreach($assignments as $product => $assignment)
    {
      $models[$product] = Arr::get($records, $assignment, null);
    }

    $this->setRecords($modelName, $models);
  }

  protected function setModifications()
  {
    /**
     * @var Product[] $productLinks
     */
    $productLinks = array();

    foreach($this->dataProvider->getData() as $product)
    {
      $productLinks[$product->id] = $product;
    }

    $criteria = new CDbCriteria();
    $criteria->addInCondition('t.parent', $this->dataProvider->getKeys());
    $modificationList = new ProductModificationList($criteria, null, false);

    foreach($modificationList->getDataProvider()->getData() as $modification)
    {
      $productLinks[$modification->parent]->addRelatedRecord('modifications', $modification, true);
    }
  }
}