<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.controllers
 */
class BaseController extends BController
{
  public function actionIndex()
  {
    if( !Yii::app()->user->isGuest )
    {
      $this->forward('help/help/index');
    }
    else
    {
      $this->actionLogin();
    }
  }

  public function actionError()
  {
    if( $error = Yii::app()->errorHandler->error )
    {
      if( $error['code'] === 404 && Yii::app()->user->isGuest )
      {
        Yii::app()->user->loginRequired();
        Yii::app()->end();
      }

      if( Yii::app()->request->isAjaxRequest )
        echo $error['message'];
      else
        $this->render('error', $error);
    }
  }

  public function actionLogout()
  {
    Yii::app()->user->logout();
    $this->redirect(Yii::app()->homeUrl);
  }

  private function actionLogin()
  {
    $model = new LoginForm();
    $ajax  = Yii::app()->request->getPost('ajax');

    if( isset($ajax) && $ajax === 'login-form' )
    {
      echo CActiveForm::validate($model);
      Yii::app()->end();
    }

    $loginForm = Yii::app()->request->getPost('LoginForm');
    if( isset($loginForm) )
    {
      $model->attributes = $loginForm;

      if( $model->validate() && $model->login() )
        $this->redirect(Yii::app()->user->returnUrl);
    }

    $this->render('login', array('model' => $model));
  }

  public function loadModel($id, $modelClass = null)
  {
    return null;
  }
}