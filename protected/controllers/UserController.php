<?php
/**
 * User: tatarinov
 * Date: 02.10.12
 */
class UserController extends FController
{
  public function filters()
  {
    return array(
      'accessControl',
    );
  }

  public function accessRules()
  {
    return array(
      array('deny',
        'actions' => array('data', 'history'),
        'users'   => array('?'),
      ),
    );
  }

  public function actionLogin()
  {
    if( !Yii::app()->user->isGuest )
      $this->redirect($this->createUrl('user/profile'), true, 200);

    //$this->socialAuth();

    $this->breadcrumbs = array('Вход');

    /**
     * @var FForm $loginForm
     */
    $loginForm = $this->loginForm;
    $loginForm->addLayoutViewParams(array('this' => $this));
    $loginForm->ajaxValidation();

    if( isset($_POST['Login']) )
    {
      $loginForm->model->attributes = $_POST['Login'];
      if( $loginForm->process() )
      {
        if( $loginForm->model->loginUser() )
        {
          $this->redirect(Yii::app()->user->returnUrl);
          Yii::app()->end();
        }
      }

      $loginForm->model->addError('Login_authError', 'Ошибка неверный логин/пароль!');
    }

    $this->render('login', array('loginForm' => $loginForm));
  }

  public function actionLogout()
  {
    $returnUrl = Yii::app()->user->returnUrl;
    Yii::app()->user->logout();
    $this->redirect($returnUrl);
    Yii::app()->end();
  }

  public function actionRegistration()
  {
    if( Yii::app()->user->isGuest )
    {
      $registrationForm = new FForm('UserRegistration', new UserRegistration());
      $registrationForm['extendedData']->model = new UserDataExtended();
      $registrationForm['extendedData']['birthday']->form = $registrationForm['extendedData'];

      $registrationForm->loadFromSession  = true;
      $registrationForm->clearAfterSubmit = true;

      $registrationForm->autocomplete = false;

      $registrationForm->ajaxValidation();

      $this->breadcrumbs = array('Регистрация нового пользователя');

      if( $registrationForm->save() )
      {
        $registrationForm->successMessage = CHtml::tag('div', array('class' => 'm20 bb center register-success'), '<span>Регистрация успешно завершена.</span>');
        Yii::app()->notification->send('userRegistrationBackend', array('model' => $registrationForm['extendedData']->model, 'userData' => $registrationForm->model));
      }

      $this->render('registration', array('registrationForm' => $registrationForm));
    }
    else
      $this->actionPrivateProfile();
  }

  public function actionData()
  {
    $userForm  = new FForm('UserRegistration', User::model()->findByPk(Yii::app()->user->getId()));
    $userForm['extendedData']->model = UserDataExtended::model()->findByPk(Yii::app()->user->getId());
    $userForm['extendedData']['birthday']->form = $userForm['extendedData'];

    $userForm->autocomplete = false;

    $userForm->ajaxValidation();

    $this->breadcrumbs = array('Профиль');

    $userForm->buttons['submit']->src = 'i/btn_send.png';

    if( $userForm->save() )
    {
      Yii::app()->user->setFlash('success', 'Изменения сохранены');
      $this->redirect($this->getCurrentUrl());
    }

    $this->render('userData', array('userForm' => $userForm));
  }

  public function actionRestore()
  {
    $this->breadcrumbs = array('Восстановление пароля');

    $restoreForm = new FForm('UserRestore', new UserRestore());
    $restoreForm->validateOnChange = false;

    if( $restoreForm->process() )
    {
      $record = $restoreForm->getModel()->findByAttributes(array('email' => $restoreForm->getModel()->email));
      $record->generateRestoreCode();
      $restoreForm->responseSuccess('Вам на E-mail отправлены дальнейшие инструкции');
    }
    else
      $this->render('restore', array('restoreForm' => $restoreForm));
  }

  public function actionRestoreConfirmed($code)
  {
    $this->breadcrumbs = array('Восстановление пароля');

    $record = UserRestore::model()->findByAttributes(array('restore_code' => $code));
    if( $record )
    {
      $record->generateNewPassword();
      $this->render('restore', array('restoreForm' => 'Новый пароль выслан на ваш E-mail.'));
    }
    else
      $this->redirect(array('user/restore'));
  }

  public function actionHistory()
  {
    $this->breadcrumbs = array('История заказов');

    $orders = array();
    $model  = Order::model();

    $filterKeys = $model->getFilterKeys(Yii::app()->user->getId());
    if( !empty($filterKeys) )
      $orders = $model->getFilteredOrders(Yii::app()->user->getId(), !empty($_GET['filter']) ? $_GET['filter'] : $filterKeys[0]['id']);

    $this->render('orderHistory', array(
      'model' => $model,
      'orders' => $orders,
      'filterKeys' => $filterKeys));
  }

  public function actionHistoryOne($id)
  {
    $order = Order::model()->findByPk($id);

    if( empty($order) )
      throw new CHttpException(404, 'Страница не найдена');

    $this->breadcrumbs = array('История заказов', 'Заказ №'.$order->id);

    $this->render('orderHistoryOne', array('order' => $order, 'backUrl' => $this->createUrl('user/history')));
  }
}