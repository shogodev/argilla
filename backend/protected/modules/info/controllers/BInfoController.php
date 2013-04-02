<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.info
 */
class BInfoController extends BController
{
  public $layout = '//layouts/column2';

  public $modelClass = 'BInfo';

  public $name = 'Информация';

  public function actionCreate()
  {
    $model = new BInfo;

    $model->parent = Yii::app()->request->getQuery('parent', BInfo::ROOT_ID);

    $this->saveModels(array($model));

    $this->render('tree', array(
      'model'   => $model,
      'path'    => $model->getStringPath($model->parent),
      'current' => $model->parent,
    ));
  }

  public function actionUpdate($id)
  {
    /**
     * @var BInfo $model
     */
    $model = $this->loadModel($id);

    $this->saveModels(array($model));

    $this->render('tree', array(
      'model'   => $model,
      'path'    => $model->getStringPath($model->id),
      'current' => $model->id,
    ));
  }

  public function actionIndex()
  {
    $this->actionCreate();
  }

  public function actionList()
  {
    $dataProvider = new BActiveDataProvider($this->modelClass);

    $this->render('list', array(
      'model' => $this->createFilterModel(),
      'dataProvider' => $dataProvider,
    ));
  }

  public function actionDragAndDrop()
  {
    if( Yii::app()->request->isAjaxRequest )
    {
      $dragModel = BInfo::model()->findByPk(Yii::app()->request->getPost('drag', null));
      $dropModel = BInfo::model()->findByPk(Yii::app()->request->getPost('drop', null));

      if( $dragModel && $dropModel )
      {
        if( $dragModel->moveAsLast($dropModel) )
        {
          $this->renderPartial('_tree', array(
            'model' => BInfo::model(),
            'current' => Yii::app()->request->getPost('current', 0)
          ));
        }

        Yii::app()->end();
      }
    }
  }
}