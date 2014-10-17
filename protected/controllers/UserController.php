<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.controllers
 */
class UserController extends FController
{
  public function actionLogin()
  {
    if( !Yii::app()->user->isGuest )
      $this->redirect($this->createUrl('userProfile/profile'), true, 200);

    $this->breadcrumbs = array('Вход');

    $loginForm = new FForm('LoginForm', new Login());
    $loginForm->action = Yii::app()->controller->createUrl('user/login');
    $loginForm->ajaxSubmit = false;
    $loginForm->validateOnChange = false;
    $loginForm->validateOnSubmit = false;
    $loginForm->autocomplete = true;

    if( $loginForm->process() )
    {
      $this->redirect(Yii::app()->user->returnUrl);
      Yii::app()->end();
    }

    $this->render('login', array('loginForm' => $loginForm));
  }

  public function actionLogout()
  {
    $returnUrl = Yii::app()->user->returnUrl;
    Yii::app()->user->logout(false);
    $this->redirect($returnUrl);
    Yii::app()->end();
  }

  public function actionRegistration()
  {
    if( Yii::app()->user->isGuest )
    {
      $this->breadcrumbs = array('Регистрация');

      $registrationForm = new FForm('UserRegistration', new User());
      $registrationForm->loadFromSession  = true;
      $registrationForm->clearAfterSubmit = true;
      $registrationForm['profile']->model = new UserProfile(User::SCENARIO_REGISTRATION);

      if( Yii::app()->request->isPostRequest )
        $registrationForm->model->email = CHtml::encode(Yii::app()->request->getParam('email', ''));

      $registrationForm->ajaxValidation();

      if( Yii::app()->request->isAjaxRequest && $registrationForm->save() )
      {

          Yii::app()->notification->send(
            'UserRegistration',
            array(
              'model' => $registrationForm->model,
              'profile' => $registrationForm['profile']->model
            ),
            $registrationForm->model->email
        );

        Yii::app()->notification->send(
          'UserRegistrationBackend',
          array(
            'model' => $registrationForm->model,
            'profile' => $registrationForm['profile']->model
          )
        );

        echo CJSON::encode(array(
          'status' => 'ok',
          'messageForm' => $this->textBlockRegister(
            'Успешная регистрация',
            'Регистрация успешно завершена'
          ),
          'removeElements' => array('registration-text')
        ));
        Yii::app()->end();
      }

      $this->render('registration', array('registrationForm' => $registrationForm));
    }
    else
    {
      $this->render('registration');
    }
  }

  public function actionRestore()
  {
    $this->breadcrumbs = array('Восстановление пароля');

    $restoreForm = new FForm('UserRestore', new RestorePassword(RestorePassword::GENERATE_RESTORE_CODE));
    $restoreForm->validateOnChange = false;
    $restoreForm->ajaxValidation();

    if( Yii::app()->request->isAjaxRequest && $restoreForm->process() )
    {
      Yii::app()->notification->send(
        'UserRequestRestorePassword',
        array('model' => $restoreForm->model),
        $restoreForm->model->email
      );

      $restoreForm->responseSuccess(Yii::app()->controller->textBlockRegister(
        'Email успешно отправлен',
        'Вам на E-mail отправлены дальнейшие инструкции'
      ));
    }
    else
      $this->render('restore', array('restoreForm' => $restoreForm));
  }

  public function actionRestoreConfirmed($code)
  {
    $this->breadcrumbs = array('Восстановление пароля');

    $restorePassword = new RestorePassword(RestorePassword::GENERATE_NEW_PASSWORD);
    $restorePassword->attributes = array('restoreCode' => $code);

    if( $restorePassword->validate() )
    {
      Yii::app()->notification->send(
        'UserRestorePassword',
        array(
          'model' => $restorePassword,
          'password' => $restorePassword->user->password
        ),
        $restorePassword->user->email
      );

      $this->render('restore', array('restoreForm' => 'Новый пароль выслан на ваш E-mail.'));
    }
    else
      $this->redirect(array('user/restore'));
  }
}