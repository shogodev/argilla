<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2016 Shogo
 * @license http://argilla.ru/LICENSE
 */
Yii::import('backend.modules.product.components.FacetIndexer');
/**
 * Class BFacetedProductParamVariant
 * @property BProductParamVariant $owner
 * @mixin DaoParametersBehavior
 */
class BFacetedProductParamVariant extends SActiveRecordBehavior
{
  protected $oldName;

  public function init()
  {
    parent::init();

    $this->attachBehavior('daoParametersBehavior', 'frontend.share.behaviors.DaoParametersBehavior');
  }

  public function afterFind($event)
  {
    $this->oldName = $this->owner->name;
  }

  public function afterSave($event)
  {
    parent::afterSave($event);

    if( $this->oldName != $this->owner->name )
      $this->reindex();
  }


  /**
   * @return string
   */
  private function reindex()
  {
    $reindexProductIdList = $this->getReindexProductIdList($this->owner->param_id);
    $removeParameterNameId = $this->owner->param_id;
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