<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
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
    $id = Yii::app()->request->getQuery('id');
    /**
     * @var BOrder $model
     */
    $model = BOrder::model()->findByPk($id);

    $model->deleted = 1;

    if( $model->save(false) )
    {
      Yii::app()->user->setFlash('success', 'Запись успешно удалена.');
    }
    else
    {
      throw new CHttpException(404, 'Ошибка! Не могу удалить запись.');
    }
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
    $attributes = Yii::app()->request->getQuery($this->modelClass);
    $modelOrder = BOrder::model()->findByPk(Arr::get($attributes, 'id'));

    $elements = Yii::app()->request->getPost('elements', array());

    foreach($elements as $key => $element)
      if( $element == 'false' )
        unset($elements[$key]);

    $modelUser = BFrontendUser::model()->findByPk(key($elements));

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

  public function actionPrint($id)
  {
    $this->layout = '//layouts/print';
    $model = $this->loadModel($id);

    if( !empty($model->user->profile->birthday) )
    {
      preg_match_all('#\d+#', $model->user->profile->birthday, $match);
      $model->user->profile->birthday = (int)$match[0][0].' '.Yii::app()->locale->getMonthName((int)$match[0][1]).' '.(int)$match[0][2].' г.';
    }

    $this->render('print', array(
      'model' => $model,
    ));
  }

  public function actionSendNotification($orderId)
  {
    $successSend = false;

    /**
     * @var BOrder $order
     */
    if( $order = $this->loadModel($orderId) )
    {
      if( $order->email )
      {
        Yii::app()->notification->send('Order', array('model' => $order), $order->email);
        Yii::app()->user->setFlash('success', 'Уведомление отправлено');
        $successSend = true;
      }
    }

    if( !$successSend )
      Yii::app()->user->setFlash('error', 'Не удалось отправить уведомление');

    Yii::app()->request->redirect($this->createUrl('/order/order/update', array('id' => $orderId)));
  }

  /**
   * @param BOrder $model
   *
   * @throws CHttpException
   */
  protected function actionSave($model)
  {
    $delivery = isset($model->delivery) ? $model->delivery : new BOrderDelivery;
    $payment = isset($model->payment) ? $model->payment : new BOrderPayment();

    $this->saveModels(array($model));
    $this->render('_form', array(
      'model' => $model,
      'modelDelivery' => $delivery,
      'modelPayment' => $payment
    ));
  }

  protected function getModelsAllowedForSave()
  {
    return array('payment' => 'BOrderPayment', 'delivery' => 'BOrderDelivery');
  }
}