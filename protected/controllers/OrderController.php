<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 */
class OrderController extends FController
{
  const CLASS_WRAPPER = 'js-wrapper-basket';

  public function actionFirstStep()
  {
    $this->breadcrumbs = array('Корзина');

    if( Yii::app()->request->isAjaxRequest )
    {
      $this->forward('basket/ajax', false);
    }
    else
    {
      $this->basket->ajaxUrl = Yii::app()->createUrl('order/firstStep');
      $this->basket->ajaxUpdate(self::CLASS_WRAPPER);
    }

    $this->renderBasketFirstStep(array());
  }

  public function actionSecondStep()
  {
    if( $this->basket->isEmpty() )
      Yii::app()->request->redirect($this->createUrl('order/firstStep'));

    $this->breadcrumbs = array('Корзина');

    $orderForm = new FForm('OrderForm', new Order());
    $orderForm->loadFromSession = true;
    $orderForm->autocomplete = true;
    $orderForm->ajaxValidation();

    if( $orderForm->save() )
    {
      $orderForm->sendNotificationBackend();
      $orderForm->sendNotification($orderForm->model->email);

      $this->basket->clear();

      echo CJSON::encode(array(
        'status' => 'ok',
        'redirect'  => $orderForm->model->getSuccessUrl(),
      ));

      Yii::app()->session['orderSuccess'] = true;
      Yii::app()->session['orderId'] = $orderForm->model->id;
      Yii::app()->end();
    }
    else
    {
      $this->render('second_step', array('form' => $orderForm, 'model' => $orderForm->model));
    }
  }

  public function actionThirdStep()
  {
    if( $this->basket->isEmpty() && !Yii::app()->session->get('orderSuccess', false) )
      Yii::app()->request->redirect($this->createUrl('order/firstStep'));

    $orderId = Yii::app()->session['orderId'];
    Yii::app()->session->remove('orderId');
    Yii::app()->session->remove('orderSuccess');

    $this->breadcrumbs = array('Корзина');
    $this->render('third_step', array('orderId' => $orderId));
  }

  private function renderBasketFirstStep($data = array())
  {
    $view = $this->basket->isEmpty() ? 'empty' : 'first_step';
    $html = array(CHtml::openTag('div', array('id' => self::CLASS_WRAPPER)));
    $html[] = $this->renderPartial($view, $data, true);
    $html[] = CHtml::closeTag('div');

    $output = implode("\n\r", $html);

    if( !Yii::app()->request->isAjaxRequest )
    {
      $output = $this->renderFile($this->getLayoutFile($this->layout), array('content' => $output), true);
      $output = $this->processOutput($output);

    }

    echo $output;
  }
} 