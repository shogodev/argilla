<?php
class BDealerController extends BController
{
  public $name = 'Дилеры';

  public $modelClass = 'BDealer';

  public $position = 10;

  /**
   * @param BActiveRecord $model
   *
   * @return mixed|void
   * @throws CHttpException
   */

  public function actionSave($model)
  {
    $models = array($model);

    if( $this->module->userDependency )
    {
      $user = $model->user_id ? BFrontendUser::model()->resetScope()->findByPk($model->user_id) : new BFrontendUser();
      $models[] = $user;
    }

    $this->performAjaxValidationForSeveralModels($models);

    if( Yii::app()->request->isPostRequest && $this->validateModels($models) )
    {
      Yii::app()->db->beginTransaction();
      $model->setAttributes(Yii::app()->request->getPost(get_class($model)));

      if( isset($user) )
      {
        $user->setAttributes(Yii::app()->request->getPost(get_class($user)));
        $user->type = BDealer::TYPE_DEALER;

        if( !$user->save() )
          throw new CHttpException(500, 'Can`t save '.get_class($model).' model');

        if( $model->isNewRecord )
          $model->user_id = $user->id;
      }

      if( !$model->save() )
        throw new CHttpException(500, 'Can`t save '.get_class($user).' model');

      Yii::app()->db->currentTransaction->commit();
      $this->redirectAfterSave($model);
    }

    $this->render('_form', array(
      'model' => $model,
      'models' => $models,
    ));
  }
}