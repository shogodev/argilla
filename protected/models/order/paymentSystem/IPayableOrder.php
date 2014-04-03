<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.order.paymentSystem
 */
interface IPayableOrder
{
  /**
   * @return integer
   */
  public function getId();

  /**
   * @return string
   */
  public function getAmount();

  /**
   * @return string
   */
  public function getCurrency();

  /**
   * @return IPayableOrderProduct[]
   */
  public function getProducts();

  /**
   * @return string
   */
  public function getUserFirstName();

  /**
   * @return string
   */
  public function getUserLastName();

  /**
   * @return string
   */
  public function getUserPatronymic();

  /**
   * @param string $format
   *
   * @return mixed
   */
  public function getUserBirthDate($format = null);

  /**
   * @return string
   */
  public function getUserEmail();

  /**
   * @return string
   */
  public function getUserPhone();

  /**
   * @return integer
   */
  public function getPaymentId();

  /**
   * @param integer $id
   *
   * @return bool
   */
  public function setPaymentId($id);

  /**
   * @return string
   */
  public function getPaymentSystem();

  /**
   * @param string $status
   *
   * @return bool
   */
  public function setStatus($status);

  /**
   * @param array $paymentData
   * @param string $errorMessage
   *
   * @return bool
   */
  public function isOrderAvailable($paymentData, &$errorMessage);

  /**
   * @param bool $result
   * @param array $paymentData
   * @param string $description
   *
   * @return mixed
   */
  public function setPaymentResult($result, $paymentData, &$description);

  /**
   * @param bool $result
   * @param array $paymentData
   * @param string $description
   *
   * @return mixed
   */
  public function setCaptureResult($result, $paymentData, &$description);
}
