<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.order
 */
/**
 * Class Order
 *
 * @method static Order model(string $className = __CLASS__)
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $name
 * @property string $email
 * @property string $phone
 * @property string $address
 * @property string $comment
 * @property string $type
 * @property float $sum
 * @property integer $ip
 * @property string $date_create
 * @property integer $status_id
 * @property string $order_comment
 * @property bool $deleted
 * @property Product[]|FBasket $basket
 * @property string $totalSum
 * @property string $deliveryPrice
 *
 * @property OrderPayment $payment
 * @property OrderDelivery $delivery
 * @property OrderProduct[] $products
 * @property OrderStatus $status
 *
 * @mixin PlatronPaymentBehavior
 */
class Order extends FActiveRecord
{
  const TYPE_FAST = 'fast';

  const TYPE_BASKET = 'basket';

  protected $fastOrderBasket;

  public function rules()
  {
    return array(
      array('name, phone', 'required', 'except' => 'fastOrder'),
      array('phone', 'required', 'on' => 'fastOrder'),
      array('email', 'email'),
      array('name, phone, address, comment', 'length', 'min' => 3, 'max' => 255),
      array('sum', 'safe'),
    );
  }

  public function attributeLabels()
  {
    return array(
      'name' => $this->isFast() ? 'Ваше имя' : 'Имя',
      'email' => 'E-mail',
      'phone' => 'Телефон',
      'comment' => 'Комментарии к заказу'
    );
  }

  /**
   * @return array
   */
  public function relations()
  {
    return array(
      'products' => [self::HAS_MANY, 'OrderProduct', 'order_id'],
      'status' => [self::BELONGS_TO, 'OrderStatus', 'status_id'],
      'delivery' => [self::HAS_ONE, 'OrderDelivery', 'order_id'],
      'payment' => [self::HAS_ONE, 'OrderPayment', 'order_id'],
    );
  }

  public function beforeSave()
  {
    // При update'е onBeforeSave в поведениях на вызывается
    if( !$this->isNewRecord )
      return true;

    $this->sum = $this->basket->getSumTotal();
    $this->ip = ip2long(Yii::app()->request->userHostAddress);
    $this->type = $this->isFast() ? self::TYPE_FAST : self::TYPE_BASKET;
    $this->user_id = !Yii::app()->user->isGuest ? Yii::app()->user->getId() : null;
    $this->date_create = date('Y-m-d H:i:s');

    return parent::beforeSave();
  }

  public function afterSave()
  {
    // При update'е onAfterSave в поведениях на вызывается
    if( !$this->isNewRecord )
      return;

    parent::afterSave();
    $this->saveProducts();
  }

  public function getBackendUrl()
  {
    return Yii::app()->request->hostInfo.'/backend/order/bOrder/update/'.$this->id;
  }

  public function setFastOrderBasket(FBasket $fastOrderBasket)
  {
    $this->fastOrderBasket = $fastOrderBasket;
  }

  public function getDate($format = 'd.m.Y H:i')
  {
    return DateTime::createFromFormat('Y-m-d H:i:s', $this->date_create)->format($format);
  }

  public function getTotalSum()
  {
    return $this->delivery ? $this->delivery->delivery_price + $this->sum : $this->sum;
  }

  public function getDeliveryPrice()
  {
    if( !$this->delivery )
      return 0;

    return $this->delivery->delivery_price;
  }

  protected function isFast()
  {
    return $this->scenario == 'fastOrder';
  }

  protected function getBasket()
  {
    if( $this->isFast() )
      return $this->fastOrderBasket;

    return Yii::app()->controller->basket;
  }

  protected function saveProducts()
  {
    foreach($this->basket as $product)
    {
      $discountPrice = $this->getDiscountPrice($product);
      $orderProduct = $this->saveModel(new OrderProduct(), array(
        'order_id' => $this->primaryKey,
        'name' => $product->name,
        'price' => $product->getPrice() + $discountPrice,
        'count' => $product->collectionAmount,
        'discount' => $discountPrice,
        'sum' => $product->getSumTotal(),
      ));

      $image = Arr::reset($product->getImages());

      $this->saveModel(new OrderProductHistory(), array(
        'order_product_id' => $orderProduct->getPrimaryKey(),
        'product_id' => $product->id,
        'url' => $product->getUrl(),
        'img' => $image ? $image->pre : '',
        'articul' => $product->articul
      ));

      $this->saveCollectionItems($product, $orderProduct);
    }
  }

  /**
   * @param Product $product
   * @param FActiveRecord $orderProduct
   * @throws CHttpException
   */
  protected function saveCollectionItems(Product $product, $orderProduct)
  {
    $this->saveItems($product->collectionItems, $orderProduct);
    $this->saveItems($product->innerCollectionItems(), $orderProduct);
  }

  /**
   * @param $item
   * @param $orderProduct
   *
   * @throws CHttpException
   */
  protected function saveCollectionItem($item, $orderProduct)
  {
    if( empty($item) || !($item->asa('collectionElement') instanceof FCollectionElementBehavior) )
      return;

    $this->saveModel(new OrderProductItem(), array(
      'order_product_id' => $orderProduct->primaryKey,
      'type' => $item->getOrderItemType(),
      'pk' => $item->getPrimaryKey(),
      'name' => $item->getOrderItemName(),
      'value' => $item->getOrderItemValue(),
      'amount' => $item->getOrderItemAmount(),
      'price' => $item->getOrderItemPrice(),
    ));
  }

  /**
   * @param FActiveRecord $model
   * @param array $attributes
   *
   * @return FActiveRecord
   * @throws CHttpException
   */
  protected function saveModel(FActiveRecord $model, array $attributes)
  {
    $model->attributes = $attributes;

    if( !$model->save() )
      throw new CHttpException(500, 'Can`t save '.get_class($model).' model');

    return $model;
  }

  /**
   * @param array $items
   * @param FActiveRecord $orderProduct
   */
  private function saveItems($items, $orderProduct)
  {
    if( empty($items) )
      return;

    foreach($items as $item)
    {
      if( $item instanceof FCollection )
      {
        foreach($item as $oneItem)
        {
          $this->saveCollectionItem($oneItem, $orderProduct);
        }
      }
      else
      {
        $this->saveCollectionItem($item, $orderProduct);
      }
    }
  }

  private function getDiscountPrice($product)
  {
    $discountPrice = 0;
    try
    {
      $discountPrice = $product->getRealDiscountPrice();
    }
    catch(Exception $e)
    {

    }

    return $discountPrice;
  }
}