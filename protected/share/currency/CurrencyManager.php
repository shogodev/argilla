<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 */
Yii::import('frontend.share.currency.CRBCurrency');

class CurrencyManager
{
  const USD = 'USD';

  const EUR = 'EUR';

  public $allowedCurrencyKeys = array(
    self::USD => 'Доллар США',
    self::EUR => 'Евро'
  );

  public function getCurrency($currencyKey, $round = 2)
  {
    if( empty($currencyKey) )
      return null;

    if( !isset($this->allowedCurrencyKeys[$currencyKey]) )
      throw new CHttpException(500, 'Неизвестный ключ валюты '.CHtml::encode($currencyKey));

     $currency = $this->getLocalRate($currencyKey);

    if( is_null($currency) )
      $currency = $this->getCRBRate($currencyKey);

    return $currency ? round($currency, $round) : null;
  }

  public function getLocalRate($currencyKey)
  {
    $localRatePrefix = array(
      self::USD => 'bux',
      self::EUR => 'euro'
    );

    $path = '/tmp/'.$localRatePrefix[$currencyKey].'4.txt';

    if( file_exists($path) )
    {
      $rate = file_get_contents($path);

      return $rate / 10000;
    }

    return null;
  }

  public function getCRBRate($currencyKey)
  {
    return CRBCurrency::getCurrency($currencyKey);
  }
}