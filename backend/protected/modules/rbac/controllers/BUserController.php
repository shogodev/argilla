<?php
/**
 * @date 31.08.2012
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @package RBAC
 */
class BUserController extends BController
{
  public $name = 'Пользователи';

  public $modelClass = 'BUser';

  /**
   * @param BActiveRecord $model
   *
   * @return mixed
   */
  protected function actionSave($model)
  {
    $this->performAjaxValidation($model);
    $attributes = Yii::app()->request->getPost(get_class($model));

    if( isset($attributes) )
    {
      $model->password = $attributes['passwordNew'];
      $model->setAttributes($attributes);

      if( $model->save() )
        $this->redirectAfterSave($model);
    }

    $this->render('_form', array('model' => $model, 'roles' => BRbacRole::getRoles()));
  }
}