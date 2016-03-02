<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 */
class RetailCrmProductList extends ProductList
{
  protected $assignments;

  protected $parameters;

  protected $images;


/*  protected function afterFetchData($event)
  {
    $keys = $this->dataProvider->getKeys(true);

    $this->setAssignmentsRetailCrm($keys);
    $this->setParametersRetailCrm($keys);
    $this->setImagesRetailCrm($keys);
    $this->setParentsRetailCrm();
  }*/

  public function getAssignmentsRetailCrm()
  {
    return $this->assignments;
  }

  public function getParametersRetailCrm()
  {
    return $this->parameters;
  }

  public function getImages()
  {
    return $this->images;
  }

/*  protected function initCriteria(CDbCriteria $criteria)
  {
    $assignment = ProductAssignment::model()->tableName();
    $assignmentCriteria = new CDbCriteria();
    $assignmentCriteria->select = 'product_id';
    $assignmentCriteria->compare('visible', 1);
    $assignmentCriteria->distinct = true;

    $command = Yii::app()->db->commandBuilder->createFindCommand($assignment, $assignmentCriteria);
    $criteria->addInCondition('t.id', $command->queryColumn());
    $criteria->compare('t.visible', 1);

    $this->criteria = $criteria;
  }*/

  protected function setParentsRetailCrm()
  {
    $criteria = new CDbCriteria();
    $criteria->addInCondition('t.id', $this->getParentIds());
    $criteria->index = 'id';
    $parents = Product::model()->findAll($criteria);

    /**
     * @var Product $product
     */
    foreach($this->dataProvider->getData() as $product)
    {
      if( !empty($product->parent) )
      {
        $product->addRelatedRecord('parentProduct', Arr::get($parents, $product->parent), false);
      }
    }
  }

  protected function setImagesRetailCrm($keys)
  {
    $criteria = new CDbCriteria();
    $criteria->compare('type', 'main');
    $criteria->addInCondition('parent', CMap::mergeArray($keys, $this->getParentIds()));

    $command = Yii::app()->db->commandBuilder->createFindCommand(ProductImage::model()->tableName(), $criteria);

    foreach($command->queryAll() as $image)
      $this->images[$image['parent']] = Yii::app()->request->getHostInfo().'/f/product/'.$image['name'];

    return $this->images;
  }

  protected function setAssignmentsRetailCrm($keys)
  {
    $criteria = new CDbCriteria(array('select' => 'a.product_id, a.id'));
    $criteria->addInCondition('a.product_id', $keys);
    $assignmentModel = new ProductAssignment();
    $assignments = $assignmentModel->getAssignments($criteria);

    $this->assignments = array();
    foreach($assignments as $assignment)
    {
      $this->assignments[$assignment['product_id']][$assignment['id']] = $assignment;
    }
  }

  protected function setParametersRetailCrm($keys)
  {
    $criteria = new CDbCriteria();
    $criteria->select = 't.*, pn.name as pn_name, pv.name as pv_name';
    $criteria->join .= ' JOIN {{product_param_name}} as pn on(pn.id = t.param_id)';
    $criteria->join .= ' JOIN {{product_param_variant}} as pv on(pv.id = t.variant_id)';
    $criteria->compare('pn.product', 1);
    $criteria->order = 'pn.name';
    $criteria->addInCondition('t.product_id', $keys);

    $builder = new CDbCommandBuilder(Yii::app()->db->getSchema());
    $command = $builder->createFindCommand(ProductParameter::model()->tableName(), $criteria, 't');
    $parameters = $command->queryAll();

    $this->parameters = array();
    foreach($parameters as $parameter)
    {
      if( !empty($parameter['pv_name']) || !empty($parameter['value']) )
      {
        $this->parameters[$parameter['product_id']][$parameter['param_id']]['id'] = $parameter['param_id'];
        $this->parameters[$parameter['product_id']][$parameter['param_id']]['name'] = $parameter['pn_name'];
        $this->parameters[$parameter['product_id']][$parameter['param_id']]['variants'][] = !empty($parameter['pv_name']) ? $parameter['pv_name'] : $parameter['value'];
      }
    }
  }
}