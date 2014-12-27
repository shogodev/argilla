<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.product
 */
class BProductColorController extends BController
{
  public $enabled = false;

  public $modelClass = 'BProductColor';

  protected function actionSave($model)
  {
    $model->product_id = Yii::app()->request->getQuery('model_id', $model->product_id);

    $this->saveModels(array($model));
    $this->render('_form', array('model' => $model));
  }
}