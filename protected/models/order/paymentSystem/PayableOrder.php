<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.order.paymentSystem
 * @property integer $id
 */
class PayableOrder implements IPayableOrder
{
  /**
   * @var Order
   */
  protected $order;

  /**
   * @var OrderPayment
   */
  protected $payment;

  public function __construct($orderId)
  {
    $this->order = Order::model()->findByPk($orderId);

    if( !$this->order )
    {
      throw new CException('Не удалось найти заказ с id = '.$orderId);
    }

    $this->payment = OrderPayment::model()->findByAttributes(array('order_id' => $orderId));

    if( !isset($this->payment) )
    {
      $this->payment = new OrderPayment();
      $this->payment->order_id = $orderId;
    }
  }

  public function getId()
  {
    return $this->order->id;
  }

  /**
   * @return string
   */
  public function getAmount()
  {
    return $this->order->sum;
  }

  /**
   * @return string
   */
  public function getCurrency()
  {
    return 'RUR';
  }

  /**
   * @return IPayableOrderProduct[]
   */
  public function getProducts()
  {
    return $this->order->products;
  }

  /**
   * @return string
   */
  public function getUserFirstName()
  {
    return $this->order->name;
  }

  /**
   * @return string
   */
  public function getUserLastName()
  {
    return $this->order->last_name;
  }

  /**
   * @return string
   */
  public function getUserPatronymic()
  {
    return $this->order->patronymic;
  }

  /**
   * @param string $format
   *
   * @return mixed
   */
  public function getUserBirthDate($format = null)
  {
    $user = $this->order->user;
    $date = null;

    if( isset($user) )
    {
      $date = $user->birthdayEncode($user->birthdayDay, $user->birthdayMouth, $user->birthdayYear, '');
    }

    return $date;
  }

  /**
   * @return string
   */
  public function getUserEmail()
  {
    return $this->order->email;
  }

  /**
   * @return string
   */
  public function getUserPhone()
  {
    return $this->order->phone;
  }

  /**
   * @return integer
   */
  public function getPaymentId()
  {
    return $this->payment->payment_id;
  }

  /**
   * @param integer $id
   *
   * @return bool
   */
  public function setPaymentId($id)
  {
    if( isset($id) && empty($this->payment->payment_id) )
    {
      $this->payment->payment_id = $id;

      return $this->payment->save(false);
    }

    return false;
  }

  /**
   * @return string
   */
  public function getPaymentSystem()
  {
    return null;
  }

  /**
   * @param string $status
   *
   * @return bool
   */
  public function setStatus($status)
  {
    $this->payment->status = $status;

    return $this->payment->save(false);
  }

  /**
   * @param array $paymentData
   * @param string $errorMessage
   *
   * @return bool
   */
  public function isOrderAvailable($paymentData, &$errorMessage)
  {
    return true;
  }

  /**
   * @param bool $result
   * @param array $paymentData
   * @param string $description
   *
   * @return mixed
   */
  public function setPaymentResult($result, $paymentData, &$description)
  {
    if( $result )
    {
      $description = 'Оплата успешно принята';

      return $this->setStatus('ok');
    }
    else
    {
      $description = 'Оплата заказа не принята';

      return $this->setStatus('failed');
    }
  }

  /**
   * @param bool $result
   * @param array $paymentData
   * @param string $description
   *
   * @return mixed
   */
  public function setCaptureResult($result, $paymentData, &$description)
  {
    $this->payment->captured_status = 'ok';

    return $this->payment->save(false);
  }
}