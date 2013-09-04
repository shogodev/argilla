<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 */
/**
 * Атрибуты:
 * @property integer      $id
 * @property integer      $user_id
 * @property string       $name
 * @property string       $email
 * @property string       $phone
 * @property string       $address
 * @property string       $comment
 * @property string       $type
 * @property string       $source
 * @property float        $sum
 * @property string       $delivery
 * @property integer      $ip
 * @property string       $date_create
 * @property string       $status
 * @property string       $order_comment
 * @property bool         $deleted
 * @property integer      $payment_id
 * @property integer      $delivery_id
 * @property Product[]|FBasket $basket
 *
 * Отношения:
 * @property DirPayment   $paymentType
 * @property DirDelivery  $deliveryType
 * @property OrderProduct $products
 */
class Order extends FActiveRecord
{
  const TYPE_FAST = 'fast';

  const TYPE_BASKET = 'basket';

  /**
   * @var string
   */
  public $historyUrl;

  public function rules()
  {
    return array(
      array('name, phone', 'required'),
      array('email', 'email'),
      array('name, phone, address, comment', 'length', 'min' => 3, 'max' => 255),
      array('payment_id, delivery_id, sum, delivery', 'safe'),
    );
  }

  public function attributeLabels()
  {
    return array(
      'name' => !$this->isFast() ?  'Ваше имя' : 'Имя получателя',
      'email' => 'E-mail',
      'phone' => 'Телефон для связи',
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
      'status' => [self::BELONGS_TO, 'OrderStatus', 'status_id'],
    );
  }

  public function beforeSave()
  {
    if( !$this->isNewRecord )
      return parent::beforeSave();

    $this->sum = $this->basket->totalSum();
    $this->ip = ip2long(Yii::app()->request->userHostAddress);
    $this->date_create = date('Y-m-d H:i:s');
    $this->source = Yii::app()->params['source'];

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

      $image = $product->getImages('main');
      $orderProductHistory = new OrderProductHistory();
      $orderProductHistory->attributes = array('order_product_id' => $orderProduct->getPrimaryKey(),
        'product_id' => $product->id,
        'url' => $product->url,
        'img' => isset($image[0]->pre) ? $image[0]->pre : '',
        'articul' => $product->articul);

      if( !$orderProductHistory->save() )
        throw new CHttpException(500, 'Can`t save '.get_class($orderProductHistory).' model');


      if( !isset($product->collectionItems['service']) || $product->collectionItems['service']->isEmpty() )
        continue;

      foreach($product->collectionItems['service'] as $item)
      {
        /**
         * @var $item Service
         */
        $productItem = new OrderProductItem();
        $productItem->attributes = array(
          'order_product_id' => $orderProduct->primaryKey,
          'type' => get_class($item),
          'pk' => $item->primaryKey,
          'name' => 'Услуга',
          'amount' => $item->collectionAmount,
          'price' => $item->price,
          'value' => $item->name,
        );

        if( !$productItem->save() )
          throw new CHttpException(500, 'Can`t save '.get_class($productItem).' model');
      }
    }

    parent::afterSave();
  }

  public function afterFind()
  {
    $this->historyUrl = Yii::app()->controller->createUrl('user/historyOne', array('id' => $this->id));
  }

  public function getFilterKeys($userId)
  {
    $data = array();

    $mounts = Yii::app()->db->createCommand()
      ->selectDistinct("DATE_FORMAT(date_create, '%m') AS mouth, DATE_FORMAT(date_create, '%Y') AS year")
      ->from($this->tableName())
      ->where("user_id = :user_id AND (deleted IS NULL OR deleted = 0)", array(':user_id' => $userId))
      ->queryAll();

    foreach($mounts as $value)
    {
      $data[] = array(
        'id'   => $value['year'].$value['mouth'],
        'name' => Yii::app()->locale->getMonthName(intval($value['mouth']), 'wide', true).' '.$value['year'],
      );
    }

   return $data;
  }

  public function getFilteredOrders($userId, $filter)
  {
    $criteria = new CDbCriteria;
    $criteria->condition = "DATE_FORMAT(date_create, '%Y%m') = :filter AND user_id = :user_id AND (deleted IS NULL OR deleted = 0)";
    $criteria->params    = array(':filter' => $filter, ':user_id' => $userId);

    return $this->findAll($criteria);
  }

  public function renderFilter($filterKeys, $htmlOptions = array())
  {
    $event   = 'change';
    $id      = 'filerDate';
    $handler = "location.href='".Yii::app()->controller->createUrl('user/history', array('filter' => ''))."' + $(this).val()";

    $cs = Yii::app()->getClientScript();
    $cs->registerCoreScript('jquery');
    $cs->registerCoreScript('yii');
    $cs->registerScript('Yii.CHtml.#filerDate' . $id, "$('#$id').on('$event', function(){{$handler}});");

    return CHtml::dropDownList('filerDate',
                                isset($_GET['filter']) ? $_GET['filter'] : '',
                                CHtml::listData($filterKeys, 'id', 'name'),
                                $htmlOptions);
  }

  public function getAdminUrl()
  {
    return Yii::app()->request->hostInfo.'/backend/order/bOrder/update/'.$this->id;
  }

  protected function isFast()
  {
    return $this->scenario == 'fastOrder';
  }

  protected function getBasket()
  {
    if( $this->isFast() )
      return Yii::app()->controller->fastOrderBasket;

    return Yii::app()->controller->basket;
  }
}