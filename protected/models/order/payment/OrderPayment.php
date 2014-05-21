<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.order.payment
 *
 * @method static OrderPayment model(string $className = __CLASS__)
 *
 * @property integer $id
 * @property integer $order_id
 * @property string $system_id
 * @property integer $payment_type_id
 * @property integer $payment_id
 * @property integer $captured_status
 *
 * @property Order $order
 */
class OrderPayment extends FActiveRecord
{
  public function relations()
  {
    return array(
      'order' => array(self::BELONGS_TO, 'Order', 'order_id'),
    );
  }

  public function rules()
  {
    return array(
      array('payment_type_id', 'safe'),
    );
  }

  protected function beforeSave()
  {
    if( $this->order && $this->order->payment_id == $this->order->getPaymentSystemTypeId() && empty($this->payment_type_id) )
      throw new CHttpException(500, 'Не задано обязательное свойство payment_type_id');

    return parent::beforeSave();
  }

  public function attributeLabels()
  {
    return CMap::mergeArray(parent::attributeLabels(), array(
      'payment_type_id' => '',
    ));
  }
}