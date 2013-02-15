<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.product
 */
class BProductTypeController extends BController
{
  public $position = 40;

  public $name = 'Типы';

  public $modelClass = 'BProductType';

  public function actionSave($model)
  {
    $assignmentModel = BProductTreeAssignment::assignToModel($model, 'section');

    $this->saveModels(array($model, $assignmentModel));
    $this->render('_form', array('model' => $model, 'assignmentModel' => $assignmentModel));
  }
}