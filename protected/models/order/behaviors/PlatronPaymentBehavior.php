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
 * @property Order $owner
 * @property OrderPayment $payment
 */
class PlatronPaymentBehavior extends SBehavior
{
  public function init()
  {
    $this->addRelations();
    $this->attachValidators();
  }

  /**
   * @return string
   */
  public function getSuccessUrl()
  {
    if( $this->owner->payment_id == $this->getPaymentSystemTypeId() )
    {
      $paymentSystem = new PlatronSystem($this->owner->id);

      return $paymentSystem->renderWidget(true);
    }
    elseif( isset(Yii::app()->controller) )
    {
      return Yii::app()->controller->createAbsoluteUrl('basket/success');
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

  public function registerPaymentScripts()
  {
    $script = "
      var id = '".$this->getPaymentSystemTypeId()."';
      var payments = $('input[name=Order\\\\[payment_id\\\\]]');
      var platron_payments = $('input[name=OrderPayment\\\\[payment_type_id\\\\]]');

      $(payments).on('change', function(){
        $(payments).filter('[type=hidden]').val('');
        $(platron_payments).filter(':checked').prop('checked', false).next().removeClass('check_radio');
      });

      $(platron_payments).on('change', function(){
        $(payments).filter('[type=hidden]').val(id);
        $(payments).filter(':checked').prop('checked', false).next().removeClass('check_radio');
      });


      $(payments.get(0)).trigger('change');
    ";
    Yii::app()->clientScript->registerScript(__CLASS__, $script, CClientScript::POS_READY);
  }

  private function addRelations()
  {
    $this->owner->metaData->addRelation('payment', array(FActiveRecord::HAS_ONE, 'OrderPayment', 'order_id'));
  }

  private function attachValidators()
  {
    $this->owner->getValidatorList()->add(
      CValidator::createValidator('CRequiredValidator', $this->owner, 'email, payment_id', array('except' => 'fastOrder'))
    );
  }
}