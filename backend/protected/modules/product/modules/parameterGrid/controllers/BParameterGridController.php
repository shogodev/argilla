<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.product.controllers
 */
class BParameterGridController extends BController
{
  public $showInMenu = false;

  public $name = 'Параметры (grid)';

  public $modelClass = 'BProductParam';

  public function afterAction($action)
  {
    parent::afterAction($action);

    if( in_array($action->id, array('toggle')) )
    {
      $model = BProductParam::model()->findByPk(Yii::app()->request->getParam('id'));
      BProduct::model()->findByPk($model->product_id)->save();
    }
  }
}