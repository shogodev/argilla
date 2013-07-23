<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.controllers
 */
class BasketController extends FController
{
  public function init()
  {
    parent::init();

    $this->processBasketAction();
  }

  public function actionIndex()
  {
    $this->breadcrumbs = array('Корзина');

    $this->render('basket');
  }

  public function actionPanel()
  {
    $this->renderPartial('/product_panel');
  }

  public function actionCheckOut()
  {
    if( $this->basket->isEmpty() )
      Yii::app()->request->redirect($this->basket->url);

    $this->breadcrumbs = array('Корзина');

    $orderForm = new FForm('Order', new Order());
    $orderForm->autocomplete = true;
    $orderForm->ajaxValidation();

    if( $orderForm->save() )
    {
      Yii::app()->notification->send('OrderBackend', array('model' => $orderForm->model));
      Yii::app()->notification->send($orderForm->model, array(), $orderForm->model->email);

      $this->basket->clear();

      echo CJSON::encode(array(
        'status' => 'ok',
        'redirect'  => $this->createAbsoluteUrl('basket/success')
      ));

      Yii::app()->session['orderSuccess'] = true;

      Yii::app();
    }
    else
    {
      if( !Yii::app()->user->isGuest )
      {
        $orderForm->model->setAttributes(array(
          'name' =>Yii::app()->user->data->name,
          'address' => Yii::app()->user->data->address,
          'email' => Yii::app()->user->email
        ));
      }

      $this->render('check_out', array('form' => $orderForm));
    }
  }

  public function actionSuccess()
  {
    if( $this->basket->isEmpty() && !Yii::app()->session->get('orderSuccess', false) )
      Yii::app()->request->redirect($this->basket->url);

    Yii::app()->session->remove('orderSuccess');

    $this->render('success');
  }

  public function actionFastOrder()
  {
    $form = $this->fastOrderForm;

    $form->ajaxValidation();

    $this->fastOrderBasket->add(Yii::app()->request->getPost($this->fastOrderBasket->keyCollection));

    if( !$this->fastOrderBasket->isEmpty() && $form->save() )
    {
      Yii::app()->notification->send('OrderBackend', array('model' => $form->model));
      Yii::app()->notification->send($form->model, array(), $form->model->email);

      echo CJSON::encode(array(
        'status' => 'ok',
        'hideElements' => array($this->fastOrderForm->id),
        'showElements' => array('order-submit-success')
      ));

      Yii::app()->end();
    }
  }

  public function actionFavoriteToBasket()
  {
    foreach($this->favorite as $item)
    {
      $data = $item->toArray();
      unset($data['index']);
      $this->basket->add($data);
    }

    $this->renderPartial('/product_panel');
  }

  protected function processBasketAction()
  {
    $request = Yii::app()->request;

    if( !$request->isAjaxRequest )
      return;

    $data = $request->getPost($this->basket->keyCollection);
    $action = $request->getPost('action');

    if( $data && $action )
    {
      switch($action)
      {
        case 'remove':
          $this->basket->remove(Arr::get($data, 'id'));
          break;

        case 'changeAmount':
          $this->basket->change($data['id'], intval($data['amount']));
        break;

        case 'add':
          $this->basket->add($data);
        break;
      }
    }
  }
}
?>