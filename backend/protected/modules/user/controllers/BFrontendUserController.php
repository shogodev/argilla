<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.user.controllers
 */
class BFrontendUserController extends BController
{
  public $name = 'Пользователи';

  public $modelClass = 'BFrontendUser';

  public $position = 10;

  public function actionSave($model)
  {
    $userProfile = !empty($model->id) ? BUserProfile::model()->findByPk($model->id) : new BUserProfile();

    $this->saveModels(array($model, $userProfile));

    $this->render('_form', array('model' => $model, 'userProfile' => $userProfile));
  }

  public function actionSearch()
  {
    /**
     * @var BFrontendUser $model
     */
    $model = $this->createFilterModel();

    $dataProvider = new BActiveDataProvider($this->modelClass, array(
      'criteria' => $model->getSearchCriteria(new CDbCriteria()),
    ));

    $this->render('index', array(
      'model' => $model,
      'searchDataProvider' => $dataProvider,
    ));
  }
}