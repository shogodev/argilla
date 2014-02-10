<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.controllers
 */
class XmlExportController extends FController
{
  public function actionIndex($id)
  {
    $method = $id.'Xml';

    if( method_exists($this, $method) )
    {
      /**
       * @var AbstractXml $xml
       */
      $xml = $this->$method();
      $xml->init();
      $xml->render();
    }
    else
    {
      throw new BadMethodCallException('Вызываемый метод не существует.');
    }
  }

  protected function yandexXml()
  {
    $criteria = new CDbCriteria();
    $criteria->compare('xml', 1);
    $criteria->compare('price', '>0');

    return Yii::createComponent(array(
      'class' => 'YandexMarketXml',
      'criteria' => $criteria
    ));
  }

  protected function priceXml()
  {
    $criteria = new CDbCriteria();

    return Yii::createComponent(array(
      'class' => 'YandexMarketXml',
      'criteria' => $criteria,
      'dataProviderClass' => 'YandexPriceDataProvider',
    ));
  }
}