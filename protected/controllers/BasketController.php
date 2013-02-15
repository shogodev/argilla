<?php
/**
 * User: tatarinov
 * Date: 05.10.12
 */
class BasketController extends FController
{
  /**
   * @var FBasket
   */
  public $basket;

  public function init()
  {
    parent::init();
    $this->basket = $this->getBasket();
  }

  public function actionIndex()
  {
    $this->breadcrumbs = array($this->basket->name);

    $orderForm = new FForm('Order', new Order());

    if( !Yii::app()->user->isGuest )
      $orderForm->getModel()->attributes = User::model()->findByPk(Yii::app()->user->getId())->attributes;

    if( $orderForm->save() )
    {
      $this->basket->removeAll();
      $this->basket->responseSuccess(
        array('updateElements' => array(
          'basket_empty' => 'Заказ успешно оформлен')
        )
      );
    }
    else
    {
      $this->render('basket', array('basket'    => $this->basket,
                                    'orderForm' => $orderForm));
    }
  }

  public function actionAdd()
  {
    if( Yii::app()->request->isAjaxRequest && isset($_POST[$this->basket->getCollectionKey()]) )
    {
      if( $this->basket->add($_POST[$this->basket->getCollectionKey()]) !== false )
        $this->basket->responseSuccess();
    }
  }

  public function actionDelete()
  {
    if( Yii::app()->request->isAjaxRequest && isset($_POST[$this->basket->getCollectionKey()]['collectionIndex']) )
    {
      $collectionIndex = $_POST[$this->basket->getCollectionKey()]['collectionIndex'];
      if( $this->basket->remove($collectionIndex) )
      {
        $this->basket->responseSuccess(
          array('removeElements' => array($this->basket->getElementId(array('row', $collectionIndex))))
        );
      }
    }
  }

  public function actionChangeCount()
  {
    if( Yii::app()->request->isAjaxRequest && isset($_POST) )
    {
      $data = array();

      foreach($this->basket->getProducts() as $product)
      {
        $key = $this->basket->getElementId(array('count', $product->collectionIndex));

        if( isset($_POST[$key]) )
          $data[$product->collectionIndex] = intval($_POST[$key]);
      }

      foreach($data as $collectionIndex => $count)
        $this->basket->changeCount($collectionIndex, $count);

      foreach($data as $collectionIndex => $count)
      {
        $product = $this->basket->getProduct($collectionIndex);
        $response[$this->basket->getElementId(array('sum', $collectionIndex))]   = Yii::app()->format->formatNumber($product->sum);
        $response[$this->basket->getElementId(array('count', $collectionIndex))] = $product->count;
      }

      $this->basket->responseSuccess(array('updateElements' => $response));
    }
  }

  public function actionFastOrder()
  {
    $fastOrderForm = $this->getFastOrderForm();
    $basket        = $this->getBasket();

    if( $_POST['FastOrder']['action'] == 'to_basket' )
    {
      if( isset($_POST['FastOrder']['product_id']) )
        $product = Product::model()->findByPk($_POST['FastOrder']['product_id']);

      if( empty($product) )
        throw new HttpException(404, 'Ошибка');

      $data = $basket->dataForSend($product->id);
      $basket->add($data[$basket->getCollectionKey()]);

      $basket->responseSuccess(array(
        'reload'   => true
      ));
    }

    if( $fastOrderForm->process() )
    {
      $data = $basket->dataForSend($fastOrderForm->model->product_id);
      $basket->add($data[$basket->getCollectionKey()]);

      $fastOrderForm->model->save(false);
      $basket->removeAll();
      $basket->responseSuccess(array(
        'hideElements'   => array('fast_order_form_block'),
        'showElements'   => array('fast_order_message_block'),
        'updateElements' => array('fast_order_message_block' => 'Заказ оформлен успешно')
      ));
    }
  }

  public function filters()
  {
    return array('ajaxOnly + add + delete + changeCount');
  }
}
?>