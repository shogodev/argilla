<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2016 Shogo
 * @license http://argilla.ru/LICENSE
 */
Yii::import('frontend.share.components.ManyRecordInserter');
Yii::import('backend.modules.product.components.FacetPropertyIterator');
Yii::import('backend.modules.product.components.FacetModificationPropertyIterator');
Yii::import('backend.modules.product.components.FacetParameterIterator');

/**
 * Class FacetIndexer
 * @mixin CurrentTransactionBehavior
 */
class FacetIndexer extends CComponent
{
  public $indexTable = '{{faceted_search}}';

  public $modificationList = array();

  /**
   * @var CDbCommandBuilder
   */
  private $builder;

  /**
   * @var ManyRecordInserter
   */
  private $manyRecordInserter;

  private $chunkSize;

  public function __construct($chunkSize = 25000)
  {
    $this->chunkSize = $chunkSize;
    $this->builder = Yii::app()->db->commandBuilder;
    $this->manyRecordInserter = new ManyRecordInserter($this->indexTable, $this->chunkSize);
    $this->manyRecordInserter->attachEventHandler('onSave', array($this, 'onSaveRecords'));

    $this->attachBehaviors($this->behaviors());
  }

  public function behaviors()
  {
    return array(
      'currentTransactionBehavior' => array('class' => 'frontend.share.behaviors.CurrentTransactionBehavior')
    );
  }

  public function reindexProducts(array $productIdList = null)
  {
    try
    {
      $this->beginTransaction();

      $this->clearIndexByProductIdList($productIdList);

      $propertyList = BFacetedParameter::model()->getProperties();
      $this->indexIterator(new FacetPropertyIterator($propertyList, $this->chunkSize, $productIdList));
      $this->indexIterator(new FacetModificationPropertyIterator($propertyList, $this->chunkSize, $productIdList));

      $parameterList = BFacetedParameter::model()->getParameters();
      $this->indexIterator(new FacetParameterIterator($parameterList, $this->chunkSize, $productIdList));

      $this->manyRecordInserter->save(true);

      $this->commitTransaction();
    }
    catch(Exception $e)
    {
      $this->rollbackTransaction();

      throw $e;
    }
  }

  public function reindexAll()
  {
    $this->reindexProducts();
  }

  public function clearIndexByParameterNameIdList(array $parameterNameIdList)
  {
    $criteria = new CDbCriteria();
    $criteria->addInCondition('param_id', $parameterNameIdList);

    $this->clearIndex($criteria);
  }

  public function clearIndexByProductIdList(array $productIdList = null)
  {
    $criteria = new CDbCriteria();

    if( !empty($productIdList) )
    $criteria->addInCondition('product_id', $productIdList);

    $this->clearIndex($criteria);
  }

  public function clearIndex(CDbCriteria $criteria = null)
  {
    $criteria = is_null($criteria) ? new CDbCriteria() : $criteria;

    $this->builder->createDeleteCommand($this->indexTable, $criteria)->execute();
  }

  public function onSaveRecords(CEvent $event)
  {
    $this->raiseEvent('onSaveRecords', $event);
  }

  protected function indexIterator(SqlIterator $iterator)
  {
    foreach($iterator as $attributes)
    {
      foreach($attributes as $attribute)
      {
        if( empty($attribute) )
          return;

        $this->manyRecordInserter->addAttributes($attribute);
        $this->manyRecordInserter->save();
      }
    }
  }
}