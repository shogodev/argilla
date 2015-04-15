<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.controllers
 */
class BasketController extends FController
{
  private $renderPanel = false;

  public function filter()
  {
    return array('ajaxOnly + ajax, fastOrder, favoriteToBasket, repeatOrder');
  }

  public function actionAjax()
  {
    $this->processBasketAction();
    $this->renderAjax();
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

    $this->renderAjax();
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

        $this->renderAjax();
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

  protected function renderAjax()
  {
    $this->renderPartial('/_basket_header');
    $this->renderPanel();
  }

  private function renderPanel()
  {
    if( $this->renderPanel )
      $this->renderPartial('/panel/panel');
  }
}