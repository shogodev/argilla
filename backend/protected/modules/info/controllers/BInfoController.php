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
    $this->performAjaxValidation($model);

    $parent     = $this->loadModel(Yii::app()->request->getQuery('parent', BInfo::ROOT_ID));
    $attributes = Yii::app()->request->getPost('BInfo');

    if( isset($attributes) )
    {
      $model->attributes = $attributes;
      if( $model->appendTo($parent) )
        $this->redirect(array('update', 'id' => $model->id));
    }

    $path = $model->getStringPath($parent->id);

    $this->render('tree', array(
      'model'   => $model,
      'path'    => $path,
      'current' => $parent->id,
    ));
  }

  public function actionUpdate($id)
  {
    /**
     * @var BInfo $model
     */
    $model = $this->loadModel($id);
    $this->performAjaxValidation($model);

    $attributes = Yii::app()->request->getPost('BInfo');

    if( isset($attributes) )
    {
      $model->attributes = $attributes;
      if( $model->saveNode() )
        $this->redirect(array('update', 'id' => $model->id));
    }

    $path = $model->getStringPath($model->id);

    $this->render('tree', array(
      'model'   => $model,
      'path'    => $path,
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
}