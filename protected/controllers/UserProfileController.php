<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.controllers
 */
class UserProfileController extends FController
{
  public $pageSize = 10;

  public function filters()
  {
    return array('accessControl');
  }

  public function accessRules()
  {
    return array(
      array('deny',
        'users' => array('?'),
      ),
    );
  }

  public function actionProfile()
  {
    $this->breadcrumbs = array(
      'Личный кабинет',
      'Мой профиль',
    );

    $model = User::model()->findByPk(Yii::app()->user->getId());

    $this->render('profile', array(
      'model' => $model,
    ));
  }

  public function actionData()
  {
    $this->breadcrumbs = array(
      'Личный кабинет',
      'Личные данные',
    );

    if( empty(Yii::app()->user->data->login) )
    {
      Yii::app()->user->data->scenario = User::SCENARIO_REGISTRATION;
      if( !empty(Yii::app()->user->data->socials) )
      {
        if( empty(Yii::app()->user->data->email) )
          Yii::app()->user->data->email = Yii::app()->user->getEmail();

        if( empty(Yii::app()->user->profile->name) )
          Yii::app()->user->profile->name = Yii::app()->user->getName();
      }
    }
    else
      Yii::app()->user->data->scenario = User::SCENARIO_CHANGE_EMAIL;

    $userForm = new FForm('UserData', Yii::app()->user->data);
    $userForm['profile']->model = Yii::app()->user->profile;
    $userForm['profile']->elements['birthday']->form = $userForm['profile'];
    $userForm->ajaxValidation();

    if( Yii::app()->request->isAjaxRequest && $userForm->save() )
    {
      $userForm->responseSuccess(Yii::app()->controller->textBlockRegister(
        'Успешное изменение пользовательских данных',
        'Изменения сохранены'
      ));
    }

    $this->render('data', array('form' => $userForm));
  }

  public function actionChangePassword()
  {
    $this->breadcrumbs = array(
      'Личный кабинет',
      'Сменить пароль',
    );
    $model = User::model()->findByPk(Yii::app()->user->getId());
    $model->scenario = User::SCENARIO_CHANGE_PASSWORD;

    $form = new FForm('UserChangePassword', $model);
    $form->ajaxValidation();

    if( Yii::app()->request->isAjaxRequest && $form->save() )
    {
      Yii::app()->notification->send('UserChangePassword', array('model' => $form->model), $form->model->email);
      $form->responseSuccess(Yii::app()->controller->textBlockRegister(
        'Успешное изменение пароля',
        'Изменения сохранены'
      ));
    }

    $this->render('change_password', array('form' => $form));
  }

  public function actionHistoryOrders()
  {
    $this->breadcrumbs = array(
      'Личный кабинет',
      'История заказов',
    );

    $orderDataProvider = new FActiveDataProvider('OrderHistory');

    $this->render('history_orders', array('orderDataProvider' => $orderDataProvider));
  }

  public function actionSocial()
  {
    $this->breadcrumbs = array(
      'Мой профиль',
      'Мои социальные сети',
    );

    $socialManager = new SocialManager();

    $this->render('social', array('socials' => $socialManager->getSocialList()));
  }

  public function actionBindSocial($service)
  {
    if( isset($service) )
    {
      /**
       * @var $eauth EAuthServiceBase
       */
      $eauth = Yii::app()->eauth->getIdentity($service);
      $eauth->redirectUrl = $this->createAbsoluteUrl('userProfile/social');
      $eauth->cancelUrl = $this->createAbsoluteUrl('userProfile/social');

      try
      {
        if( $eauth->authenticate() )
        {
          if( $eauth->isAuthenticated )
          {
            $socialManager = new SocialManager();

            if( $socialManager->isAllowedUnbind() && $socialManager->isBinded($eauth) )
              $socialManager->unbindSocial($eauth);
            else
              $socialManager->bingSocial($eauth);
          }
          else
          {
            $eauth->cancel();
          }
        }

        $this->redirect($this->createAbsoluteUrl('userProfile/social'));
      }
      catch(EAuthException $e)
      {
        Yii::app()->user->setFlash('error', 'EAuthException: '.$e->getMessage());

        $eauth->redirect($eauth->getCancelUrl());
      }
    }
  }
  public function getMenu()
  {
    $menu = array(
      array(
        'label' => 'Личные данные',
        'url' => array('userProfile/data')
      ),
      array(
        'label' => 'История заказов',
        'url' => array('userProfile/historyOrders')
      ),
          array(
            'label' => 'Мои социальный сети',
            'url' => array('userProfile/social')
          ),
      array(
        'label' => 'Сменить пароль',
        'url' => array('userProfile/changePassword')
      ),
    );

    return $menu;
  }
}