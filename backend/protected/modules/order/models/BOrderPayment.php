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
 * @property integer $payment_id
 * @property integer $captured_status
 */
class BOrderPayment extends BActiveRecord
{
  const TRANSACTION_URL = 'https://www.platron.ru/admin/transaction.php?object_id=';

  public function getTransactionUrl()
  {
    return CHtml::link($this->payment_id, self::TRANSACTION_URL.$this->payment_id);
  }
}