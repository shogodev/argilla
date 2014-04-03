<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.controllers
 */
class PaymentController extends FController
{
  public function filters()
  {
    return array(
      'postOnly + check, result, capture',
    );
  }

  public function actionCheck()
  {
    try
    {
      $paymentSystem = new PlatronSystem();
      $paymentSystem->processCheckPayment();
    }
    catch(CException $exception)
    {
      throw new CHttpException(404, $exception->getMessage());
    }
  }

  public function actionResult()
  {
    try
    {
      $paymentSystem = new PlatronSystem();
      $paymentSystem->processResultPayment();
    }
    catch(CException $exception)
    {
      throw new CHttpException(404, $exception->getMessage());
    }
  }

  public function actionCapture()
  {
    try
    {
      $paymentSystem = new PlatronSystem();
      $paymentSystem->processCapturePayment();
    }
    catch(CException $exception)
    {
      throw new CHttpException(404, $exception->getMessage());
    }
  }

  public function actionSuccess()
  {
    $this->breadcrumbs = array(
      'Оплата успешно произведена',
    );

    try
    {
      $paymentSystem = new PlatronSystem();
      $paymentSystem->successPaymentResult();
    }
    catch(CException $exception)
    {
      throw new CHttpException(404, $exception->getMessage());
    }

    $this->render('success', array(
      'order' => Order::model()->findByPk($paymentSystem->getOrderId()),
    ));
  }

  public function actionFailure()
  {
    $this->breadcrumbs = array(
      'Оплата не произведена',
    );

    try
    {
      $paymentSystem = new PlatronSystem();
      $error = $paymentSystem->failurePaymentResult();
    }
    catch(CException $exception)
    {
      throw new CHttpException(404, $exception->getMessage());
    }

    $this->render('failure', array(
      'order' => Order::model()->findByPk($paymentSystem->getOrderId()),
      'textBlock' => strtr(
        $this->textBlockRegister('Оплата не произведена', 'Оплата не произведена.', array('class' => 'success-message bb center red')),
        array('{error}' => $error)
      ),
    ));
  }

  protected function beforeAction($action)
  {
    Yii::app()->log->getRoutes()[1]->enabled = false;
    return parent::beforeAction($action);
  }
}