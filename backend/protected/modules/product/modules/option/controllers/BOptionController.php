<?php
/**
 * @author Artyom Panin <panin@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 */
class BOptionController extends BController
{
  public $enabled = false;

  public $name = 'Опции';

  public $modelClass = 'BOption';

  protected function actionSave($model)
  {
    $model->product_id = Yii::app()->request->getQuery('product_id', $model->product_id);
    parent::actionSave($model);
  }

  /**
   * @param BOption $model
   */
  protected function redirectAfterSave($model)
  {
    Yii::app()->user->setFlash('success', 'Запись успешно '.($model->isNewRecord ? 'создана' : 'сохранена').'.');

    if( Yii::app()->request->getParam('action') && Yii::app()->request->getParam('action') == 'index' )
    {
      $this->redirect(array('/product/product/update', 'id' => $model->product_id));
    }

    parent::redirectAfterSave($model);
  }
}