<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.order.paymentSystem
 */
interface IPayableOrderProduct
{
  /**
   * @return integer
   */
  public function getAmount();

  /**
   * @return array
   */
  public function getName();

  /**
   * @return float
   */
  public function getPrice();

  /**
   * @return string
   */
  public function getSection();
}