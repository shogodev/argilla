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

  public function init()
  {
    parent::init();

    $this->parametersCriteria = new CDbCriteria();
    $this->parametersCriteria->addColumnCondition(array($this->getTablePrefix().'.section' => 1, $this->getTablePrefix().'.section_list' => 1), 'OR');
  }

  protected function initCriteria()
  {
    $assignment = ProductAssignment::model()->tableName();
    $this->criteria->join .= ' JOIN `'.$assignment.'` AS a ON a.product_id = t.id '.$this->criteria->join;
    $this->criteria->distinct = true;
    $this->criteria->compare('t.visible', 1);
    $this->criteria->compare('a.visible', 1);
  }

  protected function afterFetchData($event)
  {
    $this->setImages();
    $this->setParameters();
    $this->setAssignments();
  }

  protected function setParameters()
  {
    /**
     * @var $products Product[]
     */
    $products = $this->dataProvider->getData();
    $modelName = $this->parameterNameModel;
    $names = $modelName::model()->search($this->parametersCriteria);
    $parameters = array();

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
    $criteria->addInCondition('product_id', $this->dataProvider->getKeys());
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
}