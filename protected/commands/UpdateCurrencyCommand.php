<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.commands
 */
Yii::import('backend.components.*');
Yii::import('backend.components.db.*');
Yii::import('backend.components.interfaces.*');
Yii::import('backend.modules.product.models.*');
Yii::import('frontend.share.helpers.*');
Yii::import('frontend.share.formatters.*');
Yii::import('frontend.share.validators.*');
Yii::import('ext.upload.components.*');

/**
 * Class UpdateCurrencyCommand
 *
 * Комана для обновления курса валюты товаров
 */
class UpdateCurrencyCommand extends CConsoleCommand
{
  public function actionIndex()
  {
    Yii::app()->db->beginTransaction();
    $this->updateCurrencies();
    Yii::app()->db->currentTransaction->commit();
  }

  private function updateCurrencies()
  {
    $criteria = new CDbCriteria();
    $criteria->compare('autorate_id', '<>""');

    /**
     * @var BProductCurrency $currency
     */
    foreach(BProductCurrency::model()->findAll($criteria) as $currency)
    {
      $currency->updateAutoRate();
    }
  }
}