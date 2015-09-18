<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 * @property Order $model
 */
class FOrderForm extends FForm
{
  public static $DEFAULT_FORMS_PATH = 'frontend.forms.order.';

  const DELIVERY_FORM = 'orderDeliveryForm';

  const PAYMENT_FORM = 'orderPaymentForm';

  public $loadFromSession = true;

  public $autocomplete = true;

  public function init()
  {
    parent::init();

    if( $this->getUniqueId() == $this->root->getUniqueId() )
    {
      $this->getPaymentForm()->model = new OrderPayment();
      $this->getDeliveryForm()->model = new OrderDelivery();
    }
  }
  public function getSuccessUrl()
  {
    if( $behavior = $this->getPaymentForm()->model->asa('paymentBehavior') )
    {
      try
      {
        return $behavior->getSuccessUrl();
      }
      catch(NoConfigException $e)
      {
        // Не выводим ошибку отсутствия конфига
      }
    }

    return Yii::app()->createAbsoluteUrl('order/thirdStep');
  }

  /**
   * @return FForm
   */
  public function getPaymentForm()
  {
    return $this->elements[self::PAYMENT_FORM];
  }

  /**
   * @return FForm
   */
  public function getDeliveryForm()
  {
    return $this->elements[self::DELIVERY_FORM];
  }
}