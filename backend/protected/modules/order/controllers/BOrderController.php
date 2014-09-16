<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.order.controllers
 */
class BOrderController extends BController
{
  public $name = 'Заказы';

  public $modelClass = 'BOrder';

  public $position = 10;

  public function filters()
  {
    return CMap::mergeArray(parent::filters(), array(
      'ajaxOnly + setUser',
    ));
  }
  public function actions()
  {
    $actions = parent::actions();
    unset($actions['delete']);
    return $actions;
  }

  public function actionDelete()
  {
    $id   = Yii::app()->request->getQuery('id');
    $data = BOrder::model()->findByPk($id);

    $data->deleted = 1;

    if( $data->save() )
    {
      Yii::app()->user->setFlash('success', 'Запись успешно удалена.');
    }
    else
      throw new CHttpException(404, 'Ошибка! Не могу удалить запись.');
  }

  public function actionOrderProducts($id)
  {
    $model = $this->loadModel($id);
    $this->render('orderProducts', array(
      'model' => $model,
    ));
  }

  public function actionSetUser()
  {
    $ids        = Yii::app()->request->getPost('ids', array());
    $attributes = Yii::app()->request->getQuery($this->modelClass);

    $modelOrder = BOrder::model()->findByPk(Arr::get($attributes, 'id'));
    $modelUser  = BFrontendUser::model()->findByPk(Arr::reset($ids));

    if( !$modelOrder || !$modelUser )
      throw new CHttpException(404, 'Некорректный запрос.');

    if( $modelOrder->setUser($modelUser) )
    {
      Yii::app()->user->setFlash('success', 'Пользователь успешно установлен.');
      Yii::app()->end();
    }
    else
      throw new CHttpException(404, 'Ошибка! Пользователь не установлен.');
  }

  public function actionUpdatePaymentStatus($orderId)
  {
    $paymentSystem = new PlatronSystem($orderId);
    $paymentSystem->getPaymentStatus();
  }

  public function actionCapturePayment($orderId)
  {
    $paymentSystem = new PlatronSystem($orderId);
    $paymentSystem->getCapturePayment();
  }
}