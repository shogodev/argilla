<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.product
 */
class BProductController extends BController
{
  public $position = 10;

  public $name = 'Продукты';

  public $modelClass = 'BProduct';

  public function actionUpdateAssignment($id = 0)
  {
    $response = array();
    $model    = $id ? $this->loadModel($id) : new BProduct();
    $data     = Yii::app()->request->getPost('BProduct');

    foreach($data['inputs'] as $key => $input)
    {
      $assignModel = $model->assignment ? Arr::reduce($model->assignment) : new BProductAssignment;
      $assignModel->setAttribute($data['attribute'], $data['value']);
      $response[$key] = $assignModel->renderAjaxHtml($model, $input['type'], $data['attribute'], $key);
    }

    echo CJavaScript::jsonEncode($response);
    Yii::app()->end();
  }

  /**
   * @param BProduct|CActiveForm $model
   *
   * @return mixed|void
   */
  protected function actionSave($model)
  {
    $assignmentModel = $model->assignment ? $model->assignment : array(new BProductAssignment);

    // привязанное событие будет выполняться в транзакции saveModels
    $model->attachEventHandler('onAfterSave', array($this, 'saveParameters'));

    $model->attachEventHandler('onAfterSave', array($this, 'saveProductAssignment'));
    $model->attachEventHandler('onBeforeValidate', array($this, 'beforeValidate'));

    $this->saveModels(array($model));

    $this->render('_form', array(
      'model'           => $model,
      'parameters'      => BProductParam::model()->getParameters($model),
      'assignmentModel' => $assignmentModel,
    ));
  }

  protected function beforeValidate(CEvent $event)
  {
    $event->sender->setScenario('validation');
  }

  protected function saveProductAssignment(CEvent $event)
  {
    $data        = Yii::app()->request->getPost('BProduct');
    $model       = BProductAssignment::model();
    $fields      = $model->getFields();
    $assignments = Arr::extract($data, array_keys($fields));

    if( !empty($assignments) )
      $model->saveAssignments($event->sender, $assignments);

  }

  protected function saveParameters(CEvent $event)
  {
    $parameters = Yii::app()->request->getPost('BProductParamName');

    if( !empty($parameters) )
      BProductParam::model()->setParameters($event->sender, $parameters);
  }
}