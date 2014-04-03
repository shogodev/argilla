<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 */
interface IPaymentSystem
{
  /**
   * @param bool $captureOutput
   *
   * @return string
   */
  public function renderWidget($captureOutput = false);

  /**
   * Инициализация платежа для оправки его в платежную систему для получения оплаты
   */
  public function getPayment();

  /**
   * Получение статуса платежа у платежной системы
   */
  public function getPaymentStatus();

  /**
   * Запрос на проведение клиринга по платежной транзакции
   */
  public function getCapturePayment();

  /**
   * Проверка плетежной системы возможности совершить платеж
   */
  public function processCheckPayment();

  /**
   * Уведомление платежной системой магазина о результате платежа
   */
  public function processResultPayment();

  /**
   * Уведомление магазина платежной системой о завершении операции клиринга
   */
  public function processCapturePayment();

  /**
   * Переход на страницу удачной оплаты
   */
  public function successPaymentResult();

  /**
   * Переход на страницу неудачной оплаты
   */
  public function failurePaymentResult();
}