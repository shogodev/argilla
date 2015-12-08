<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 */

/**
 * Class OrderPayment
 *
 * @property integer $id
 * @property integer $order_id
 * @property integer $payment_type_id
 * @property string $system_id
 * @property integer $system_payment_type_id
 * @property integer $payment_id
 * @property integer $captured_status
 *
 * @property Order $order
 * @property OrderPaymentType $paymentType
 * @property PlatronPaymentType $systemPaymentType
 *
 * @mixin PlatronPaymentBehavior
 */
class OrderPayment extends FActiveRecord
{
  public function behaviors()
  {
    return array(
      'paymentBehavior' => 'frontend.models.order.behaviors.PlatronPaymentBehavior',
    );
  }

  public function rules()
  {
    return array(
      array('payment_type_id', 'required'),
      array('payment_type_id, system_payment_type_id', 'safe'),
    );
  }

  public function relations()
  {
    return array(
      'paymentType' => array(self::BELONGS_TO, 'OrderPaymentType', 'payment_type_id'),
      'systemPaymentType' => array(self::BELONGS_TO, 'PlatronPaymentType', 'system_payment_type_id'),
    );
  }

  protected function beforeSave()
  {
    if( $this->order_id && $this->payment_type_id == $this->getPaymentSystemTypeId() && empty($this->payment_type_id) )
      throw new CHttpException(500, 'Не задано обязательное свойство system_payment_type_id');

    return parent::beforeSave();
  }

  public function attributeLabels()
  {
    return CMap::mergeArray(parent::attributeLabels(), array(
      'payment_type_id' => 'Метод оплаты',
      'system_payment_type_id' => ''
    ));
  }
}