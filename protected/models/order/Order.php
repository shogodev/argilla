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
 * @property string $source
 * @property float $sum
 * @property string $delivery
 * @property integer $ip
 * @property string $date_create
 * @property string $status
 * @property string $order_comment
 * @property bool $deleted
 * @property integer $payment_id
 * @property integer $delivery_id
 * @property Product[]|FBasket $basket
 *
 * Отношения:
 * @property DirPayment $paymentType
 * @property DirDelivery $deliveryType
 * @property OrderProduct[] $products
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
      array('payment_id, delivery_id, sum, delivery', 'safe'),
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
      'payment' => [self::BELONGS_TO, 'DirPayment', 'payment_id'],
      'delivery' => [self::BELONGS_TO, 'DirDelivery', 'delivery_id'],
      //'status' => [self::BELONGS_TO, 'OrderStatus', 'status_id'],
    );
  }

  public function beforeSave()
  {
    if( !$this->isNewRecord )
      return parent::beforeSave();

    $this->sum = $this->basket->totalSum();
    $this->ip = ip2long(Yii::app()->request->userHostAddress);
    $this->date_create = date('Y-m-d H:i:s');
    //$this->delivery_id = DirDelivery::SELF_DELIVERY;

    if( $this->isFast() )
      $this->type = self::TYPE_FAST;
    else
      $this->type = self::TYPE_BASKET;

    if( !Yii::app()->user->isGuest )
      $this->user_id = Yii::app()->user->getId();

    return parent::beforeSave();
  }

  public function afterSave()
  {
    if( !$this->isNewRecord )
      return parent::afterSave();

    $this->saveProducts();
  }

  public function getAdminUrl()
  {
    return Yii::app()->request->hostInfo.'/backend/order/bOrder/update/'.$this->id;
  }

  public function setFastOrderBasket(FBasket $fastOrderBasket)
  {
    $this->fastOrderBasket = $fastOrderBasket;
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
      $orderProduct = new OrderProduct();
      $orderProduct->attributes = [
        'order_id' => $this->primaryKey,
        'name' => $product->name,
        'price' => $product->price,
        'count' => $product->collectionAmount,
        'discount' => $product->discount,
        'sum' => $product->sum,
      ];

      if( !$orderProduct->save() )
        throw new CHttpException(500, 'Can`t save '.get_class($orderProduct).' model');
      $image = Arr::reset($product->getImages());
      $orderProductHistory = new OrderProductHistory();
      $orderProductHistory->attributes = array('order_product_id' => $orderProduct->getPrimaryKey(),
                                               'product_id' => $product->id,
                                               'url' => $product->url,
                                               'img' => isset($image) ? $image->pre : '',
                                               'articul' => $product->articul);

      if( !$orderProductHistory->save() )
        throw new CHttpException(500, 'Can`t save '.get_class($orderProductHistory).' model');

      $this->saveParameters($product, $orderProduct);
    }
  }

  /**
   * @param Product $product
   * @param OrderProduct $orderProduct
   * @throws CHttpException
   */
  protected function saveParameters($product, $orderProduct)
  {
    /**
     * @var ProductParameter|null $size
     */
    if( $size = $product->getCollectionItems('size') )
    {
      $productItem = new OrderProductItem();
      $productItem->attributes = array(
        'order_product_id' => $orderProduct->primaryKey,
        'type' => 'size',
        'pk' => $size->primaryKey,
        'name' => $size->parameterName->name,
        'value' => $size->variant->name,
      );

      if( !$productItem->save() )
        throw new CHttpException(500, 'Can`t save '.get_class($productItem).' model');
    }

    if( $parameterName = $product->getProductColorParameter() )
    {
      $parameters = $parameterName->parameters;
      $parameter = reset($parameters);
      $productItem = new OrderProductItem();
      $productItem->attributes = array(
        'order_product_id' => $orderProduct->primaryKey,
        'type' => 'color',
        'pk' => $parameter->id,
        'name' => $parameterName->name,
        'value' => $parameterName->value,
      );

      if( !$productItem->save() )
        throw new CHttpException(500, 'Can`t save '.get_class($productItem).' model');
    }
  }
}