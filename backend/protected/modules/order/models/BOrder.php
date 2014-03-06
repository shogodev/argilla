<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>, Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.order.models
 *
 * @method static BOrder model(string $class = __CLASS__)
 *
 * @property integer $id
 * @property integer $user_id
 * @property string  $name
 * @property string  $email
 * @property string  $address
 * @property string  $phone
 * @property string  $comment
 * @property string  $type
 * @property string  $sum
 * @property integer $ip
 * @property string $date_create
 * @property integer $status_id
 * @property string $order_comment
 * @property integer $deleted
 * @property BFrontendUser $user
 * @property BOrderStatus $status
 * @property BOrderProduct[] $products
 * @property BOrderStatusHistory[] $history
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

  public function relations()
  {
    return array(
      'products' => array(self::HAS_MANY, 'BOrderProduct', 'order_id'),
      'user' => array(self::BELONGS_TO, 'BFrontendUser', 'user_id'),
      'status' => array(self::BELONGS_TO, 'BOrderStatus', 'status_id'),
      'payment_type' => array(self::BELONGS_TO, 'BDirPayment', 'payment_id'),
      'history' => array(self::HAS_MANY, 'BOrderStatusHistory', 'order_id'),
      'orderPayDetails' => array(self::BELONGS_TO, 'BOrderPayDetails', array('id' => 'order_id'))
    );
  }

  public function rules()
  {
    return array(
      array('name', 'required'),
      array('email', 'email'),
      array('address, comment, phone', 'safe'),
      array('status_id', 'numerical', 'integerOnly' => true),
      array('sum', 'numerical'),
      array('order_comment, delivery_id, payment_id', 'safe'),

      array('id, type, sum, date_create_from, date_create_to, user_id', 'safe', 'on' => 'search'),
    );
  }

  public function attributeLabels()
  {
    return CMap::mergeArray(parent::attributeLabels(), array(
      'name' => 'Имя',
      'date_create_from' => 'Дата с...',
      'date_create_to' => 'по ...',
      'user_id' => 'Пользователь',
      'type' => 'Тип заказа',
      'status_id' => 'Статус',
      'order_comment' => 'Комментарий менеджера',
      'delivery_id' => 'Метод доставки',
      'payment_id' => 'Метод оплаты',
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

  /**
   * @param CDbCriteria $criteria
   */
  protected function addUserCondition(CDbCriteria $criteria)
  {
    if( preg_match("/^\d+$/", $this->user_id) )
    {
      $criteria->addSearchCondition('phone', $this->user_id);
    }
    else if( preg_match("/^[a-z@.\-_]+$/", $this->user_id) )
    {
      $criteria->addSearchCondition('email', $this->user_id);
    }
    else
    {
      $criteria->addSearchCondition('name', $this->user_id);
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

    $this->addUserCondition($criteria);

    if( !empty($this->date_create_from) || !empty($this->date_create_to) )
      $criteria->addBetweenCondition('date_create', Utils::dayBegin($this->date_create_from), Utils::dayEnd($this->date_create_to));

    return $criteria;
  }
}