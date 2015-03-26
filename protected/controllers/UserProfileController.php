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
  public function filters()
  {
    return array('accessControl');
  }

  public function accessRules()
  {
    return array(
      array('deny',
        'actions' => array('profile', 'data', 'changePassword', 'currentOrders', 'historyOrders'),
        'users'   => array('?'),
      ),
    );
  }

  public function actionProfile()
  {
    $this->breadcrumbs = array(
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
      'Мой профиль',
      'Личные данные',
    );

    $this->activeUrl = array('userProfile/data');

    $userForm = new FForm('UserData', UserProfile::model()->findByPk(Yii::app()->user->getId()));
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
      'Мой профиль',
      'Сменить пароль',
    );

    $this->activeUrl = array('userProfile/changePassword');

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

  public function actionCurrentOrders()
  {
    $this->breadcrumbs = array(
      'Мои заказы',
      'Текущие заказы',
    );

    $this->activeUrl = array('user/currentOrders');
    $orders = OrderHistory::model()->findAll();

    $this->render('current_orders', array('orders' => $orders));
  }

  public function actionHistoryOrders()
  {
    $this->breadcrumbs = array(
      'Мои заказы',
      'История заказов',
    );

    $this->activeUrl = array('user/history');
    $orders = OrderHistory::model()->findAll();

    $this->render('history_orders', array('orders' => $orders));
  }

  public function getMenu()
  {
    $menu = array(
      array(
        'label' => '<span class="accordion-menu-heading icon-my-profile">Мой профиль</span>',
        'url' => '',
        'items' => array(
          array(
            'label' => 'Личные данные',
            'url' => array('userProfile/data')
          ),
          array(
            'label' => 'Сменить пароль',
            'url' => array('userProfile/changePassword')
          )
        )
      ),
      array(
        'label' => '<span class="accordion-menu-heading icon-my-orders">Мои заказы</span>',
        'url' => '',
        'items' => array(
          array(
            'label' => 'Текущие заказы',
            'url' => array('userProfile/currentOrders')
          ),
          array(
            'label' => 'История заказов',
            'url' => array('userProfile/historyOrders')
          )
        )
      ),
    );

    return $menu;
  }
}