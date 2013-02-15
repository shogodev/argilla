<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.product
 */
class BProductParamNameController extends BController
{
  public $position = 80;

  public $name = 'Параметры';

  public $modelClass = 'BProductParamName';

  public function actionCreate()
  {
    $model = new $this->modelClass;
    $model->parent = Yii::app()->request->getQuery('parent', BProductParamName::ROOT_ID);
    $this->actionSave($model);
  }

  public function actionUpdate($id)
  {
    $this->actionSave($this->loadModel($id));
  }

  public function actionDeleteVariant()
  {
    if( Yii::app()->request->isAjaxRequest )
    {
      $id     = Yii::app()->request->getPost('id');
      $model  = BProductParamVariant::model()->findByPk($id);
      $result = $model->delete();

      if( !$result )
        throw new CHttpException(500, 'Не могу удалить запись.');
    }
    else
      throw new CHttpException(500, 'Некорректный запрос.');
  }

  protected function actionSave($model)
  {
    if( $model->parent == BProductParamName::ROOT_ID )
    {
      $this->saveGroup($model);
      return;
    }

    $this->saveModels(array($model));
    $this->render('_form', array(
      'model' => $model,
    ));
  }

  protected function saveGroup($model)
  {
    $assignmentModel = $model->assignment ? $model->assignment : new BProductParamAssignment();

    if( $model->isNewRecord )
      $assignmentModel->type_id = Yii::app()->request->getQuery('type_id');

    $this->saveModels(array($model, $assignmentModel));
    $this->render('_form', array(
      'model' => $model,
      'assignmentModel' => $assignmentModel,
    ));
  }

  public function saveBProductParamVariant($variants, BActiveRecord $parent)
  {
    if( !empty($variants) )
      $parent->saveRelatedModels('variants', $variants);
  }
}