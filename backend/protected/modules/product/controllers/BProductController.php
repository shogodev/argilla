<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.product.controllers
 */
class BProductController extends BController
{
  public $position = 10;

  public $name = 'Продукты';

  public $modelClass = 'BProduct';

  /**
   * @throws CHttpException
   */
  public function actionCopyProduct()
  {
    if( Yii::app()->request->isPostRequest )
    {
      $copier = new BProductCopier(Yii::app()->request->getPost('id'));
      $productId = $copier->copy(Yii::app()->request->getPost('withImages', false));

      if( !$productId )
        throw new CHttpException(500, 'Невозможно скопировать продукт');

      echo CJavaScript::jsonEncode(array(
        'url' => $this->createUrl('product/update', array('id' => $productId)),
      ));
      Yii::app()->end();
    }
  }

  public function actionUpdateAssignment($id = 0)
  {
    $response = array();
    $model = $id ? $this->loadModel($id) : new BProduct();
    $data = Yii::app()->request->getPost('BProduct');

    foreach($data['inputs'] as $key => $input)
    {
      $assignModel = $model->assignment ? Arr::reduce($model->assignment) : new BProductAssignment;
      $assignModel->setAttribute($data['attribute'], $data['value']);
      $response[$key] = $assignModel->renderAjaxHtml($model, $data['attribute'], $key);
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
    $model->getEventHandlers('onAfterSave')->insertAt(0, array($this, 'saveParameters'));
    $model->getEventHandlers('onAfterSave')->insertAt(1, array($this, 'saveProductAssignment'));

    $this->saveModels(array($model));

    $view = empty($model->parent) ? '_form' : 'product.views.product.modification._modification_form';

    $this->render($view, array(
      'model' => $model,
      'assignmentModel' => $assignmentModel,
    ));
  }

  protected function saveProductAssignment(CEvent $event)
  {
    $data = Yii::app()->request->getPost('BProduct');
    $model = BProductAssignment::model();
    $fields = $model->getFields();
    $assignments = Arr::extract($data, array_keys($fields));

    if( !empty($assignments) )
      $model->saveAssignments($event->sender, $assignments);
  }

  protected function saveParameters(CEvent $event)
  {
    $parameters = Yii::app()->request->getPost('BProductParamName');

    if( !empty($parameters) )
      BProductParam::model()->saveParameters($event->sender, $parameters);
  }
}