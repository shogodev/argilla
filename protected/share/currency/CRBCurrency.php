<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 */
class CRBCurrency
{
  const EUR = 'EUR';

  const USD = 'USD';

  private static $xml;

  public static $url = 'http://www.cbr.ru/scripts/XML_daily.asp';

  public static function getCurrency($currencyKey)
  {
    $xml = self::getXml();
    $xml = new SimpleXMLElement($xml);

    for($i = 0; $i < $xml->count(); $i++)
    {
      if($xml->Valute[$i]->CharCode == $currencyKey)
        return floatval(str_replace(',', '.', $xml->Valute[$i]->Value));
    }

    return null;
  }

  private static function getXml()
  {
    if( is_null(self::$xml) )
    {
      $curl = new Curl();
      $result = $curl->get(self::$url);

      if( $curl->getLastError() )
        throw new CHttpException(500, 'Ошибка. '.$curl->getLastError());

      self::$xml = $result;
    }

    return self::$xml;
  }
}