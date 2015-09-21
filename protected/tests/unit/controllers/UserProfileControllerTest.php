<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package 
 */
class UserProfileControllerTest extends CDbTestCase
{
  /**
   * @var UserController
   */
  private $controller;

  /**
   * @var User
   */
  private $user;

  protected $fixtures = array(
    'user' => 'User',
    'user_profile' => 'UserProfile',
  );

  public function setUp()
  {
    $this->controller = Arr::get(Yii::app()->createController('userProfile'), 0);
    Yii::app()->setController($this->controller);

    $this->user = User::model()->findByPk(1);

    $_SESSION = array();
  }

  /**
   * @expectedException TRedirectException
   * @expectedExceptionMessage Location: /user/login
   */
  public function testProfileAccess()
  {
    $this->controller->run('profile');
  }

  /**
   * @expectedException TRedirectException
   * @expectedExceptionMessage Location: /user/login
   */
  public function testDataAccess()
  {
    $this->controller->run('profile');
  }

  /**
   * @expectedException TRedirectException
   * @expectedExceptionMessage Location: /user/login
   */
  public function testChangePasswordAccess()
  {
    $this->controller->run('profile');
  }

  /**
   * @expectedException TRedirectException
   * @expectedExceptionMessage Location: /user/login
   */
  public function testCurrentOrdersAccess()
  {
    $this->controller->run('profile');
  }

  /**
   * @expectedException TRedirectException
   * @expectedExceptionMessage Location: /user/login
   */
  public function testHistoryOrdersAccess()
  {
    $this->controller->run('profile');
  }

  public function testProfileRender()
  {
    $this->login();

    ob_start();
    $this->controller->run('profile');
    $html = ob_get_clean();
    $this->assertContains('User name 1', $html);
  }

  public function testDataRender()
  {
    $this->login();

    ob_start();
    $this->controller->run('data');
    $html = ob_get_clean();
    $this->assertContains('UserProfile[name]', $html);
  }

  /**
   * @expectedException TEndException
   */
  public function testChangeDataFailed()
  {
    $this->login();

    Yii::app()->request->setAjax(array('UserProfile' => array('name' => '', 'phone' => '12345')));

    $this->setOutputCallback(function($data) {
      $validateErrors = json_decode(Arr::get(json_decode($data, true), 'validateErrors'), true);
      $this->assertEquals(array(
        'UserProfile_name' => array('Необходимо заполнить поле «Имя».'),
        'User_email' => array('Необходимо заполнить поле «E-mail».'),
        'User_login' => array('Необходимо заполнить поле «Логин».'),
        'User_password' => array('Необходимо заполнить поле «Пароль».'),
        'User_password_confirm' => array('Необходимо заполнить поле «Подтверждение пароля».'),
      ), $validateErrors);
    });

    $this->controller->run('data');
  }

  /**
   * @expectedException TEndException
   */
  public function testChangeDataSuccess()
  {
    $this->login();

    Yii::app()->request->setAjax(array(
      'UserProfile' => array(
        'name' => 'new name',
        'phone' => '12345'
      ),
      'User' => array(
        'email' => 'test@email.com',
        'login' => 'testUser',
        'password' => 'testPassword',
        'confirmPassword' => 'testPassword',
      )
    ));

    $this->setOutputCallback(function($data) {
      return Arr::get(CJSON::decode($data), 'messageForm') == 'Изменения сохранены' ? 'Changes save success' : 'failed';
    });

    $this->expectOutputRegex("/Changes save success/");
    $this->controller->run('data');
  }

  public function testChangePasswordRender()
  {
    Yii::app()->request->clearAjax();
    $this->login();

    ob_start();
    $this->controller->run('changePassword');
    $html = ob_get_clean();
    $this->assertContains('User[oldPassword]', $html);
    $this->assertContains('User[confirmPassword]', $html);
    $this->assertContains('User[password]', $html);
  }

  /**
   * @expectedException TEndException
   */
  public function testChangePasswordFailed()
  {
    $this->login();

    Yii::app()->request->setAjax(array('User' => array('oldPassword' => '1234', 'confirmPassword' => '12345', 'password' => '12345')));
    $this->setOutputCallback(function($data) {
      $validateErrors = json_decode(Arr::get(json_decode($data, true), 'validateErrors'), true);
      $this->assertEquals(array('User_oldPassword' => array('Не правильно введен Старый пароль')), $validateErrors);
    });
    $this->controller->run('changePassword');
  }

  /**
   * @expectedException TEndException
   */
  public function testChangePasswordSuccess()
  {
    $this->login();

    Yii::app()->request->setAjax(array('User' => array('oldPassword' => '123', 'confirmPassword' => '12345', 'password' => '12345')));
    $this->setOutputCallback(function($data) {
      return Arr::get(CJSON::decode($data), 'messageForm') == "Изменения сохранены" ? 'Changes save success' : 'failed';
    });
    $this->expectOutputRegex("/Changes save success/");
    $this->controller->run('changePassword');
  }

  private function login()
  {
    $loginModel = new Login();
    $loginModel->setAttributes(array('login' => 'user', 'password' => '123'));
    $loginModel->authenticate(array(), array());
  }
}