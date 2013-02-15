<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.order.controllers
 */
class BOrderController extends BController
{
  public $name = 'Заказы';

  public $modelClass = 'BOrder';

  public $position = 10;

  public function actions()
  {
    $actions = parent::actions();
    unset($actions['delete']);
    return $actions;
  }

  public function actionDelete()
  {
    if( Yii::app()->request->isPostRequest )
    {
      $id   = Yii::app()->request->getQuery('id');
      $data = BOrder::model()->findByPk($id);

      $data->deleted = 1;

     if( $data->save() )
      {
        Yii::app()->user->setFlash('success', 'Запись успешно удалена.');
      }
      else
        throw new CHttpException(400, 'Не могу удалить запись.');
    }
    else
      throw new CHttpException(400, 'Некорректный запрос.');
  }

  public function actionChangeStatus()
  {
    if( Yii::app()->request->isAjaxRequest && isset($_POST['id']) )
    {
      $model  = BOrder::model()->findByPk(intval($_POST['id']));
      $result = $model->changeStatus($_POST['status'], $_POST['order_comment']);

      if( $result === true )
      {
        if( $_POST['status'] == BOrder::STATUS_CONFIRMED )
          Yii::app()->notification->send('BackendOrderSuccess', array('model' => $model, 'orderComment' => $_POST['order_comment']), $model->email);
        else if( $_POST['status'] == BOrder::STATUS_CANCELED )
          Yii::app()->notification->send('BackendOrderCanceled', array('model' => $model, 'orderComment' => $_POST['order_comment']), $model->email);

        echo 'ok';
      }
      else
        echo $result;

      Yii::app()->end();
    }
  }
}