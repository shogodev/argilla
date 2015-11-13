<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>, Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.order.models
 *
 * @method static BOrder model(string $class = __CLASS__)
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $name
 * @property string $email
 * @property string $phone
 * @property string $address
 * @property string $comment
 * @property string $type
 * @property string $sum
 * @property integer $ip
 * @property string $date_create
 * @property integer $status_id
 * @property string $order_comment
 * @property integer $deleted
 * @property BFrontendUser $user
 * @property BOrderStatus $status
 * @property BOrderProduct[] $products
 * @property BOrderStatusHistory[] $history
 * @property BOrderPayment $payment
 * @property BOrderDelivery $delivery
 * @property string $totalSum
 */
class BOrder extends BActiveRecord
{
  const TYPE_FAST   = 'fast';
  const TYPE_BASKET = 'basket';

  public $typeLabel = array(
    self::TYPE_FAST   => 'Быстрый',
    self::TYPE_BASKET => 'Корзина',
  );

  public $date_create_from;

  public $date_create_to;

  public $userProfile;

  public function relations()
  {
    return array(
      'products' => array(self::HAS_MANY, 'BOrderProduct', 'order_id'),
      'user' => array(self::BELONGS_TO, 'BFrontendUser', 'user_id'),
      'status' => array(self::BELONGS_TO, 'BOrderStatus', 'status_id'),
      'payment_type' => array(self::BELONGS_TO, 'BOrderPaymentType', 'payment_id'),
      'history' => array(self::HAS_MANY, 'BOrderStatusHistory', 'order_id'),
      'orderPayDetails' => array(self::BELONGS_TO, 'BOrderPayDetails', array('id' => 'order_id')),
      'payment' => [self::HAS_ONE, 'BOrderPayment', 'order_id'],
      'delivery' => array(self::HAS_ONE, 'BOrderDelivery', 'order_id'),
    );
  }

  public function rules()
  {
    return array(
      array('name', 'required'),
      array('email', 'email'),
      array('comment, phone', 'safe'),
      array('status_id', 'numerical', 'integerOnly' => true),
      array('sum', 'numerical'),
      array('order_comment', 'safe'),

      array('id, type, sum, date_create_from, date_create_to, user_id, userProfile', 'safe', 'on' => 'search'),
    );
  }

  public function attributeLabels()
  {
    return CMap::mergeArray(parent::attributeLabels(), array(
      'name' => 'Имя',
      'date_create_from' => 'Дата с...',
      'date_create_to' => 'по ...',
      'userProfile' => 'Пользователь',
      'type' => 'Тип заказа',
      'status_id' => 'Статус',
      'order_comment' => 'Комментарий менеджера',
      'sum' => 'Стоимость заказа',
      'totalSum' => 'Итого (с доставкой)'
    ));
  }

  public function defaultScope()
  {
    return array(
      'condition' => 'deleted = :deleted',
      'params' => array(
        ':deleted' => 0,
      ),
      'order' => 'date_create DESC',
    );
  }

  /**
   * @return array
   */
  public function getProducts()
  {
    $products = array();

    foreach($this->products as $product)
    {
      $products = array_merge($products, array($product), $product->getItems());
    }

    return $products;
  }

  /**
   * @param BFrontendUser $user
   *
   * @return bool
   */
  public function setUser(BFrontendUser $user)
  {
    $this->user_id = $user->id;
    return $this->save();
  }

  public function getPadId()
  {
    return str_pad($this->id, 10, 0, STR_PAD_LEFT);
  }

  public function getDate($format = 'd.m.Y H:i')
  {
    return DateTime::createFromFormat('Y-m-d H:i:s', $this->date_create)->format($format);
  }

  public function recalc($save = true)
  {
    if( $save )
      $this->refresh();

    $this->sum = 0;

    foreach($this->products as $product)
      $this->sum += $product->sum;

    if( $save )
      $this->save();
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


  /**
   * @param CDbCriteria $criteria
   */
  protected function addUserCondition(CDbCriteria $criteria)
  {
    if( preg_match("/^[\d\(\)\-\ +]+$/", $this->userProfile) )
    {
      $criteria->addSearchCondition('phone', $this->userProfile);
    }
    else if( preg_match("/^[a-z0-9@.\-_]+$/", $this->userProfile) )
    {
      $criteria->addSearchCondition('email', $this->userProfile);
    }
    else
    {
      $criteria->addSearchCondition('name', $this->userProfile);
    }
  }

  protected function beforeSave()
  {
    if( parent::beforeSave() )
    {
      if( !$this->isNewRecord )
      {
        /**
         * @var BOrder $oldModel
         */
        $oldModel = $this->findByPk($this->getPrimaryKey());
        if( $oldModel->status_id != $this->status_id )
          $this->modelChangeStatus($oldModel);
      }

      $this->recalc(false);

      return true;
    }

    return false;
  }

  /**
   * @param BOrder $oldModel
   */
  protected function modelChangeStatus(BOrder $oldModel)
  {
    if( $status = $this->status )
    {
      BOrderStatusHistory::model()->add($oldModel, $status);
      Yii::app()->notification->send('Order'.Utils::toCamelCase($status->sysname).'Backend', array('model' => $this), $this->email);
    }
  }

  /**
   * @param CDbCriteria $criteria
   *
   * @return CDbCriteria
   */
  protected function getSearchCriteria(CDbCriteria $criteria)
  {
    $criteria = new CDbCriteria;

    $criteria->compare('id', $this->id);
    $criteria->compare('status_id', $this->status_id);
    $criteria->compare('sum', $this->sum);
    $criteria->compare('user_id', $this->user_id);

    $this->addUserCondition($criteria);

    if( !empty($this->date_create_from) || !empty($this->date_create_to) )
      $criteria->addBetweenCondition('date_create', Utils::dayBegin($this->date_create_from), Utils::dayEnd($this->date_create_to));

    return $criteria;
  }
}