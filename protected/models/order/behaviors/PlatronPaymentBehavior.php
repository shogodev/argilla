<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.order.behaviors
 */

Yii::import('frontend.models.order.payment.*');
Yii::import('frontend.models.order.paymentSystem.*');
Yii::import('frontend.models.order.paymentSystem.platron.*');

/**
 * Class PlatronPaymentBehavior
 *
 * Поведение для работы с оплатами заказа через Платрон
 *
 * @property OrderPayment $owner
 * @property OrderPayment $payment
 */
class PlatronPaymentBehavior extends SBehavior
{
  /**
   * @return string
   */
  public function getSuccessUrl()
  {
    if( $this->owner->payment_type_id == $this->getPaymentSystemTypeId() )
    {
      $paymentSystem = new PlatronSystem($this->owner->order_id);

      return $paymentSystem->renderWidget(true);
    }
    elseif( isset(Yii::app()->controller) )
    {
      return Yii::app()->controller->createAbsoluteUrl('order/thirdStep');
    }

    return null;
  }

  /**
   * @return integer
   */
  public function getPaymentSystemTypeId()
  {
    return OrderPaymentType::E_PAY;
  }
}