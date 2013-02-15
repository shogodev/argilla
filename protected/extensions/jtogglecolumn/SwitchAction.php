<?php

/**
 * CToggleColumn class file.
 * @author Nikola Trifunovic <johonunu@gmail.com>
 * @link http://www.trifunovic.me/
 * @copyright Copyright &copy; 2012 Nikola Trifunovic
 * @license http://www.yiiframework.com/license/
 */
class SwitchAction extends CAction
{
  public function run($id, $attribute)
  {
    if( Yii::app()->request->isPostRequest )
    {
      // we only allow deletion via POST request
      $model = $this->controller->loadModel($id);
      $model->updateAll(array($attribute => 0));
      $model->$attribute = 1;
      $model->save(false);

      // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
      if( !isset($_GET['ajax']) )
        $this->controller->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
    }
    else
      throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
  }
}

?>
