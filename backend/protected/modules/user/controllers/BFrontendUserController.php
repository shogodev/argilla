<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.user.controllers
 */
class BFrontendUserController extends BController implements ICoordinateSetter
{
  public $name = 'Пользователи';

  public $modelClass = 'BFrontendUser';

  public $position = 10;

  public function actionSave($model)
  {
    $userExtendedData = !empty($model->id) ? BUserDataExtended::model()->findByPk($model->id) : new BUserDataExtended();

    if( empty($model->type) )
      $model->type = BFrontendUser::TYPE_USER;

    $this->saveModels(array($model, $userExtendedData));

    $this->render('_form', array('model' => $model, 'userExtendedData' => $userExtendedData));
  }

  public function actionSetCoordinates($id, $attribute)
  {
    $model     = BUserDataExtended::model()->findByPk($id);
    $attribute = get_class($model).'_'.$attribute;

    $this->render('coordinate_setter', array(
      'model' => $model,
      'id' => $id,
      'attribute' => $attribute,
    ));
  }
}