<?php
mb_internal_encoding("UTF-8");
mb_http_output("UTF-8" );

Yii::import('backend.modules.product.models.*');
Yii::import('backend.models.behaviors.*');
Yii::import('backend.components.*');
Yii::import('backend.components.db.*');
Yii::import('backend.components.interfaces.*');
Yii::import('frontend.extensions.upload.components.*');
Yii::import('frontend.share.*');
Yii::import('frontend.share.behaviors.*');
Yii::import('frontend.share.formatters.*');
Yii::import('frontend.share.validators.*');
Yii::import('frontend.share.helpers.*');

class SaveProductsCommand extends CConsoleCommand
{
  public function actionIndex()
  {
    $criteria = new CDbCriteria();
    $criteria->select = 'id';
    $command = Yii::app()->db->schema->commandBuilder->createFindCommand(BProduct::model()->tableName(), $criteria);

    foreach($command->queryColumn() as $productId)
    {
      $product = BProduct::model()->findByPk($productId);
      $product->save();
      $product->detachBehaviors();
    }
  }
}