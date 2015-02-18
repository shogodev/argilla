<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
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

    if( Yii::app()->request->isAjaxRequest )
    {
      $this->renderPartial('/_basket_header');
      $this->renderPartial('/panel/panel');
      $this->renderPartial('index');
    }
    else
      $this->render('index');
  }

  public function actionAdd()
  {
    $this->renderPartial('/_basket_header');
    $this->renderPartial('/panel/panel');
  }

  public function actionCheckOut()
  {
    if( $this->basket->isEmpty() )
      Yii::app()->request->redirect($this->createUrl('basket/index'));

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
      $this->render('check_out', array('form' => $orderForm, 'model' => $orderForm->model));
    }
  }

  public function actionSuccess()
  {
    if( $this->basket->isEmpty() && !Yii::app()->session->get('orderSuccess', false) )
      Yii::app()->request->redirect($this->createUrl('basket/index'));

    $orderId = Yii::app()->session['orderId'];
    Yii::app()->session->remove('orderId');
    Yii::app()->session->remove('orderSuccess');

    $this->breadcrumbs = array('Корзина');
    $this->render('success', array('orderId' => $orderId));
  }

  public function actionFastOrder()
  {
    $form = $this->fastOrderForm;
    $form->ajaxValidation();

    $fastOrderBasket = new FBasket('fastOrderBasket', array(), false);
    $fastOrderBasket->add(Yii::app()->request->getPost($this->basket->keyCollection));
    $form->model->setFastOrderBasket($fastOrderBasket);

    if( !$fastOrderBasket->isEmpty() && $form->save() )
    {
      Yii::app()->notification->send('FastOrderBackend', array('model' => $form->model), null, 'backend');
      Yii::app()->notification->send('FastOrder', array('model' => $form->model), $form->model->email);

      echo CJSON::encode(array(
        'status' => 'ok',
        'hideElements' => array($form->id),
        'showElements' => array($this->basket->fastOrderFormSuccessId)
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

  public function actionRepeatOrder()
  {
    $data = Yii::app()->request->getPost($this->basket->keyCollection);
    $orderId = Arr::get($data, 'order-id');

    try
    {
      /**
       * @var OrderHistory $order
       */
      if( $order = OrderHistory::model()->findByPk($orderId) )
      {
        foreach($order->products as $orderProduct)
        {
          $data = array(
            'type' => 'product',
            'id' => $orderProduct->history->product_id,
            'amount' => $orderProduct->count,
            'items' => array()
          );

          if( $options = $orderProduct->getItems('ProductOption') )
          {
            foreach($options as $option)
            {
              $data['items']['options'][] = array('id' => $option->pk, 'type' => $option->type);
            }
          }

          if( $ingredients = $orderProduct->getItems('ProductIngredientAssignment') )
          {
            foreach($ingredients as $ingredient)
            {
              $data['items']['ingredients'][] = array(
                'id' => $ingredient->pk,
                'type' => $ingredient->type,
                'amount' => $ingredient->amount
              );
            }
          }
          $this->basket->add($data);
        }

        $this->actionAdd();
      }
    }
    catch(CHttpException $e)
    {
      $e->handled = true;
      throw new CHttpException(500, 'Ошибка. Невозможно выполнить повтрный заказ');
    }
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
          $index = Arr::get($data, 'index');

          if( is_null($index) )
            $index = $this->basket->getIndex($data);

          if( is_null($index) || !$this->basket->exists($index) )
            throw new CHttpException(500, 'Данный продукт уже удален. Обновите страницу.');

          $this->basket->remove($index);
        break;

        case 'changeAmount':
          if( !$this->basket->exists($data['index']) )
            throw new CHttpException(500, 'Продукт не найден. Обновите страницу.');
          $amount = intval($data['amount']);
          $this->basket->changeAmount($data['index'], $amount > 0 ? $amount : 1);
        break;

        case 'changeOptions':
          if( !$this->basket->exists($data['index']) )
            throw new CHttpException(500, 'Продукт не найден. Обновите страницу.');

          $items = Arr::get($this->basket[$data['index']]->getCollectionElement()->toArray(), 'items', array());
          $items['options'] = $data['options'];
          $this->basket->changeItems($data['index'], $items);
        break;

        case 'add':
          $this->basket->add($data);
        break;
      }
    }
  }
}