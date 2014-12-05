<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.rbac.controllers
 */
class BUserController extends BController
{
  public $name = 'Пользователи';

  public $modelClass = 'BUser';

  public $position = 10;

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
      $model->setAttributes($attributes);

      if( $model->save() )
        $this->redirectAfterSave($model);
    }

    $this->render('_form', array(
      'model' => $model,
      'roles' => BRbacRole::getRoles())
    );
  }
}