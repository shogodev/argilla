<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.order
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
 * @property string $delivery_sum
 * @property integer $ip
 * @property string $date_create
 * @property integer $status_id
 * @property string $order_comment
 * @property bool $deleted
 * @property integer $payment_id
 * @property integer $delivery_id
 * @property Product[]|FBasket $basket
 *
 * @property OrderPaymentType $paymentType
 * @property OrderDeliveryType $deliveryType
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
      array('payment_id, delivery_id, sum, delivery_sum', 'safe'),
    );
  }

  public function behaviors()
  {
    return array(
      'paymentBehavior' => 'frontend.models.order.behaviors.PlatronPaymentBehavior',
    );
  }

  public function attributeLabels()
  {
    return array(
      'name' => $this->isFast() ? 'Ваше имя' : 'Имя',
      'email' => 'E-mail',
      'phone' => 'Телефон',
      'address' => 'Адрес доставки',
      'delivery_id' => 'Способ доставки',
      'payment_id' => 'Методы оплаты',
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
      'paymentType' => [self::BELONGS_TO, 'OrderPaymentType', 'payment_id'],
      'deliveryType' => [self::BELONGS_TO, 'OrderDeliveryType', 'delivery_id'],
    );
  }

  public function beforeSave()
  {
    if( !$this->isNewRecord )
      return parent::beforeSave();

    $this->delivery_id = !empty($this->delivery_id) ? $this->delivery_id : null;
    $this->delivery_sum = null;

    $this->sum = $this->basket->getSumTotal() + $this->delivery_sum;
    $this->ip = ip2long(Yii::app()->request->userHostAddress);
    $this->type = $this->isFast() ? self::TYPE_FAST : self::TYPE_BASKET;
    $this->user_id = !Yii::app()->user->isGuest ? Yii::app()->user->getId() : null;
    $this->date_create = date('Y-m-d H:i:s');

    return parent::beforeSave();
  }

  public function afterSave()
  {
    if( !$this->isNewRecord )
      parent::afterSave();

    $this->saveProducts();
  }

  public function getBackendUrl()
  {
    return Yii::app()->request->hostInfo.'/backend/order/bOrder/update/'.$this->id;
  }

  public function getSuccessUrl()
  {
    if( $behavior = $this->asa('paymentBehavior') )
    {
      try
      {
        return $behavior->getSuccessUrl();
      }
      catch(NoConfigException $e)
      {
        // Не выводим ошибку отсутствия конфига
      }
    }

    return Yii::app()->createAbsoluteUrl('order/thirdStep');
  }

  public function setFastOrderBasket(FBasket $fastOrderBasket)
  {
    $this->fastOrderBasket = $fastOrderBasket;
  }

  public function getDate($format = 'd.m.Y H:i')
  {
    return DateTime::createFromFormat('Y-m-d H:i:s', $this->date_create)->format($format);
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
      $orderProduct = $this->saveModel(new OrderProduct(), array(
        'order_id' => $this->primaryKey,
        'name' => $product->name,
        'price' => $product->price,
        'count' => $product->collectionAmount,
        'discount' => $product->discount,
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
}