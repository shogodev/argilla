<?php
/**
 * @author    Sergey Glagolev <glagolev@shogo.ru>
 * @link      https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license   http://argilla.ru/LICENSE
 *
 * @method static BOrderPayment model(string $className = __CLASS__)
 *
 * @property integer $id
 * @property integer $order_id
 * @property integer $payment_type_id
 * @property string $system_id
 * @property integer $system_payment_type_id
 * @property integer $payment_id
 * @property integer $captured_status
 */
class BOrderPayment extends BActiveRecord
{
  const TRANSACTION_URL = 'https://www.platron.ru/admin/transaction.php?object_id=';

  public function rules()
  {
    return array(
      array('payment_type_id', 'required'),
      array('order_id, payment_type_id, system_payment_type_id', 'safe'),
    );
  }

  public function getTransactionUrl()
  {
    return CHtml::link($this->payment_id, self::TRANSACTION_URL.$this->payment_id);
  }

  public function attributeLabels()
  {
    return CMap::mergeArray(parent::attributeLabels(), array(
      'payment_type_id' => 'Способ оплаты'
    ));
  }
}