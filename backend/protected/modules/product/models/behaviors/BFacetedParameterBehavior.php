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
 * Class BFacetedParameterBehavior
 * @property BProductParamName $owner
 * @mixin DaoParametersBehavior
 */
class BFacetedParameterBehavior extends SActiveRecordBehavior
{
  /**
   * @var integer
   */
  private $selection;

  public function init()
  {
    parent::init();

    $this->attachBehavior('daoParametersBehavior', 'frontend.share.behaviors.DaoParametersBehavior');
  }

  public function beforeSave($event)
  {
    $this->selection = $this->owner->selection;
  }

  public function afterSave($event)
  {
    $this->isSelectionChanged() && $this->owner->selection ? $this->add() : $this->remove();
  }

  private function add()
  {
    if( $this->find() === null )
    {
      $model = new BFacetedParameter();
      $model->parameter = $this->owner->id;
      $model->save();

      $this->reindex();
    }
  }

  private function remove()
  {
    if( $this->find() )
    {
      BFacetedParameter::model()->deleteAll($this->getCriteria());

      $this->reindex();
    }
  }

  /**
   * @return bool
   */
  private function isSelectionChanged()
  {
    return $this->selection == $this->owner->selection;
  }

  /**
   * @return BFacetedParameter
   */
  private function find()
  {
    return BFacetedParameter::model()->find($this->getCriteria());
  }

  /**
   * @return CDbCriteria
   */
  private function getCriteria()
  {
    $criteria = new CDbCriteria();
    $criteria->compare('parameter', $this->owner->id);

    return $criteria;
  }

  /**
   * @return string
   */
  private function reindex()
  {
    $reindexProductIdList = $this->getReindexProductIdList($this->owner->id);
    $removeParameterNameId = $this->owner->id;
    Yii::app()->attachEventHandler('onEndRequest', function($event) use($reindexProductIdList, $removeParameterNameId) {
      ViewHelper::showFlash('Индексация фильтра началсь и может занять несколько минут');

      Utils::finishRequest();
      Utils::longLife(60);

      $facetIndexer = new FacetIndexer();

      $facetIndexer->clearIndexByParameterNameIdList(array($removeParameterNameId));
      $facetIndexer->reindexProducts($reindexProductIdList);
    });
  }

  private function getReindexProductIdList($parameterNameId)
  {
    $criteria = new CDbCriteria();
    $criteria->distinct = true;
    $criteria->select = 'product_id';
    $criteria->compare('param_id', $parameterNameId);

    return CHtml::listData($this->getParametersByCriteria($criteria, false, 'product_id'), 'product_id', 'product_id');
  }
}