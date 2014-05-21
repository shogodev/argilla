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
        'actions' => array('data', 'profile', 'history', 'historyOne', 'profile', 'password', 'status'),
        'users'   => array('?'),
      ),
    );
  }

  public function actionLogin()
  {
    if( !Yii::app()->user->isGuest )
      $this->redirect($this->createUrl('user/profile'), true, 200);

    $this->breadcrumbs = array('Вход');

    $loginForm = new FForm('LoginForm', new Login());
    $loginForm->action = Yii::app()->controller->createUrl('user/login');
    $loginForm->ajaxSubmit = false;
    $loginForm->autocomplete = true;
    $loginForm->ajaxValidation();

    if( $attributes = Yii::app()->request->getPost('Login') )
    {
      $loginForm->model->attributes = $attributes;

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
    Yii::app()->user->logout(false);
    $this->redirect($returnUrl);
    Yii::app()->end();
  }

  public function actionRegistration()
  {
    if( Yii::app()->user->isGuest )
    {
      $this->breadcrumbs = array('Регистрация');

      $registrationForm = new FForm('UserRegistration', new UserRegistration());
      $registrationForm->loadFromSession  = true;
      $registrationForm->clearAfterSubmit = true;
      $registrationForm['extendedData']->model = new UserDataExtended('registration');

      if( Yii::app()->request->isPostRequest )
        $registrationForm->model->email = CHtml::encode(Yii::app()->request->getParam('email', ''));

      $registrationForm->ajaxValidation();

      if( Yii::app()->request->isAjaxRequest && $registrationForm->save() )
      {
        Yii::app()->notification->send(
          $registrationForm->model,
          array(
            'userData' => $registrationForm['extendedData']->model,
            'password' => Yii::app()->request->getParam('UserRegistration')['password']
          ),
          $registrationForm->model->email
        );

        Yii::app()->notification->send(
          'UserRegistrationBackend',
          array(
            'model' => $registrationForm->model,
            'userData' => $registrationForm['extendedData']->model
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
  }

  public function actionData()
  {
    $this->breadcrumbs = array(
      'Мой профиль' => array('user/profile'),
      'Личные данные',
    );

    $userForm  = new FForm('UserDataForm', User::model()->findByPk(Yii::app()->user->getId()));
    $userForm['extendedData']->model = UserDataExtended::model()->findByPk(Yii::app()->user->getId());
    $userForm['extendedData']['birthday']->form = $userForm['extendedData'];
    $userForm->ajaxValidation();

    if( Yii::app()->request->isAjaxRequest && $userForm->save() )
    {
      $userForm->responseSuccess(Yii::app()->controller->textBlockRegister(
        'Успешное изменение пользовательских данных',
        'Изменения сохранены'
      ));
    }

    $this->render('data', array('userForm' => $userForm));
  }

  public function actionProfile()
  {
    $this->breadcrumbs = array(
      'Мой профиль',
    );

    $userModel = User::model()->findByPk(Yii::app()->user->getId());
    $extendedDataModel = UserDataExtended::model()->findByPk(Yii::app()->user->getId());

    $this->render('profile', array(
      'userModel' => $userModel,
      'extendedDataModel' => $extendedDataModel,
    ));
  }

  public function actionPassword()
  {
    $this->breadcrumbs = array(
      'Мой профиль' => array('user/profile'),
      'Сменить пароль',
    );

    $form = new FForm('UserPasswordForm', User::model()->findByPk(Yii::app()->user->getId()));
    $form->ajaxValidation();

    if( $form->model )
      $form->model->setScenario('changePassword');

    if( Yii::app()->request->isAjaxRequest && $form->save() )
    {
      $form->responseSuccess(Yii::app()->controller->textBlockRegister('Успешное изменение пароля', 'Изменения сохранены'));
    }

    $this->render('password', array('form' => $form));
  }

  public function actionRestore()
  {
    $this->breadcrumbs = array('Восстановление пароля');

    $restoreForm = new FForm('UserRestore', new UserRestore());
    $restoreForm->validateOnChange = false;
    $restoreForm->ajaxValidation();

    if( Yii::app()->request->isAjaxRequest && $restoreForm->process() )
    {
      $record = $restoreForm->getModel()->findByAttributes(array('email' => $restoreForm->getModel()->email));
      $record->generateRestoreCode();
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
    $this->breadcrumbs = array(
      'Мой профиль' => array('user/profile'),
      'Мои заказы',
    );

    $model  = OrderHistory::model();
    $orders = $model->user(Yii::app()->user->getId())->findAll();

    $this->render('history', array(
      'model' => $model,
      'orders' => $orders,
    ));
  }

  public function actionStatus()
  {
    $this->breadcrumbs = array(
      'Мой профиль' => array('user/profile'),
      'Мой статус',
    );

    $userModel = User::model()->findByPk(Yii::app()->user->getId());
    $extendedDataModel = UserDataExtended::model()->findByPk(Yii::app()->user->getId());

    $this->render('status', array(
      'userModel' => $userModel,
      'extendedDataModel' => $extendedDataModel,
    ));
  }

  public function actionHistoryOne($id)
  {
    $order = Order::model()->findByPk($id);

    if( empty($order) )
      throw new CHttpException(404, 'Страница не найдена');

    $this->breadcrumbs = array('История заказов', 'Заказ №'.$order->id);

    $this->render('orderHistoryOne', array('order' => $order, 'backUrl' => $this->createUrl('user/history')));
  }

  public function deleteMenuStatusIfEmpty($menu, $route)
  {
    $data = array();
    /** @var  UserDataExtended $extendedDataModel */
    $extendedDataModel = UserDataExtended::model()->findByPk(Yii::app()->user->getId());
    if($extendedDataModel->discount_id == 0)
    {
      foreach($menu as $item)
      {
        if($item['url'][0] !== $route)
          $data[] = $item;
      }
      return $data;
    }

    return $menu;
  }
}