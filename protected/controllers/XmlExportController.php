<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.controllers
 */
class XmlExportController extends FController
{
  protected function beforeAction($action)
  {
    Yii::app()->log->getRoutes()[1]->enabled = false;
    return parent::beforeAction($action);
  }

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

  protected function sitemapXml()
  {
    $path = Yii::getPathOfAlias('frontend.models.sitemap.generators');
    $generatorFactory = new GeneratorFactory($path, Yii::app()->controller);

    return Yii::createComponent(array(
      'class' => 'SitemapXml',
      'filePath' => 'f/xml/sitemap.xml',
      'routes' => SitemapRoute::model()->findAll(),
      'generators' => $generatorFactory->getGenerators(),
    ));
  }
}