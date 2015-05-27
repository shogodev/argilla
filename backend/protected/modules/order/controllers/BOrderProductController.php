<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 */
class BOrderProductController extends BController
{
  public $name = 'Продукты заказа';

  public $modelClass = 'BOrderProduct';

  public $showInMenu = false;

  public function actionDelete()
  {
    /**
     * @var BOrderProduct $orderProduct
     */
    if( $orderProduct = $this->loadModel(Yii::app()->request->getParam('id')) )
    {
      $order = $orderProduct->order;
      $action = new BDefaultActionDelete($this, 'delete');
      $action->run();
      $order->recalc();
    }
    else
      throw new CHttpException(500, 'Ошибка удаления!');
  }

  public function actionOnflyedit()
  {
    Yii::import('ext.onflyedit.OnFlyEditAction');

    /**
     * @var BOrderProduct $orderProduct
     */
    if( $orderProduct = $this->loadModel(Yii::app()->request->getParam('id')) )
    {
      $action = new OnFlyEditAction($this, 'OnFlyEdit');
      $action->run();
      $orderProduct->recalc();
    }
    else
      throw new CHttpException(500, 'Ошибка записи!');
  }

  public function actionAddProducts()
  {
    $order = BOrder::model()->findByPk(Yii::app()->request->getQuery('orderId'));

    if( $order && $elements = Yii::app()->request->getPost('elements') )
    {
      Yii::app()->db->beginTransaction();

      $selectedProducts = array();

      foreach($elements as $productId => $element)
      {
        if( $element === "true" )
        {
          $selectedProducts[] = intval($productId);
        }
      }

      foreach($selectedProducts as $productId)
      {
        if( $product = BProduct::model()->findByPk($productId) )
        {
          $this->saveProduct($product, $order->id);
        }
      }
      Yii::app()->db->getCurrentTransaction()->commit();

      $order->recalc();
    }
  }

  private function saveProduct(BProduct $product, $orderId)
  {
    $orderProduct = new OrderProduct();
    $orderProduct->setAttributes(array(
      'order_id' => $orderId,
      'name' => $product->name,
      'price' => $product->price,
      'count' => 1,
      'discount' => 0,
      'sum' => $product->price,
    ), false);

    if( !$orderProduct->save() )
      throw new CHttpException(500, 'Can`t save '.get_class($orderProduct).' model');

    $image = BProductImg::model()->findByAttributes(array('parent' => $product->id, 'type' => 'main'));

    $orderProductHistory = new OrderProductHistory();
    $orderProductHistory->setAttributes(array(
      'order_product_id' => $orderProduct->getPrimaryKey(),
      'product_id' => $product->id,
      'url' => '/'.$product->url.'/',
      'img' => $image ? Yii::app()->request->hostInfo.'/f/product/pre_'.$image->name : '',
      'articul' => $product->articul
    ), false);

    if( !$orderProductHistory->save() )
      throw new CHttpException(500, 'Can`t save '.get_class($orderProductHistory).' model');
  }
} 