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
        'actions' => array('profile', 'data', 'changePassword', 'currentOrders', 'historyOrders'),
        'users'   => array('?'),
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
        'label' => 'Сменить пароль',
        'url' => array('userProfile/changePassword')
      ),
    );

    return $menu;
  }
}