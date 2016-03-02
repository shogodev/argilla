<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 */
Yii::import('ext.retailcrm.components.*');
Yii::import('ext.retailcrm.components.xml.*');
Yii::import('ext.retailcrm.components.product.*');
/**
 * Class RetailCrmController
 */
class RetailCrmController extends FController
{
  protected function beforeAction($action)
  {
    if( !empty( Yii::app()->log) )
      Yii::app()->log->getRoutes()[1]->enabled = false;

    return parent::beforeAction($action);
  }

  public function actionIcml()
  {
    Yii::app()->retailCrm->registerEventEndExportIcml();

    try
    {
      $criteria = new CDbCriteria();

      /**
       * @var RetailCrmXml $xml
       */
      $xml = Yii::createComponent(array(
        'class' => 'RetailCrmXml',
        'criteria' => $criteria,
        'dataProviderClass' => 'RetailCrmDataProvider',
        'filePath' => 'f/xml/retail_crm.icml',
        'template' => 'yandexMarket',
      ));

      $xml->init();

      if( isset($xml->dataProvider) )
      {
        $xml->dataProvider->catalogStructure = array(
          'section',
          'type'
        );
        $xml->buildXml();
      }

      $xml->render();
    }
    catch(CException $e)
    {
      Yii::app()->retailCrm->getLogger()->error($e->getMessage());
    }
  }
}