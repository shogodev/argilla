<?php

/**
 * CToggleColumn class file.
 * @author Nikola Trifunovic <johonunu@gmail.com>
 * @link http://www.trifunovic.me/
 * @copyright Copyright &copy; 2012 Nikola Trifunovic
 * @license http://www.yiiframework.com/license/
 */
class ToggleAction extends CAction
{
  public function run($id, $attribute)
  {
    if( Yii::app()->request->isPostRequest )
    {
      /**
       * @var CActiveRecord
       */
      $model             = $this->controller->loadModel($id);
      $model->$attribute = ($model->$attribute == 0) ? 1 : 0;

      if( in_array('nestedSetBehavior', array_keys($model->behaviors())) )
        $model->saveNode();
      else
        $model->save(false);

      // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
      if( !isset(Yii::app()->request->isAjaxRequest) )
        $this->controller->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
    }
    else
      throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
  }
}

?>
