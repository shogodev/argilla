<?php
class BDealerFilialController extends BController
{
  public $name = 'Филиалы';

  public $modelClass = 'BDealerFilial';

  public $enabled = false;

  public function actions()
  {
    return CMap::mergeArray(parent::actions(), array(
      'coordinates' => array(
        'class' => 'CoordinateAction',
        'modelName' => $this->modelClass,
        'addressAttribute' => 'fullAddress'
      )
    ));
  }

  /**
   * @param BDealerFilial $model
   *
   * @return mixed|void
   */
  protected function actionSave($model)
  {
    $model->dealer_id = Yii::app()->request->getQuery('model_id', $model->dealer_id);
    parent::actionSave($model);
  }
}