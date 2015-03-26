<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package 
 */
class UserControllerTest extends CTestCase
{
  /**
   * @var UserController
   */
  private $controller;

  /**
   * @var User
   */
  private $restoreUser;

  public function setUp()
  {
    $this->controller = Arr::get(Yii::app()->createController('user'), 0);
    Yii::app()->setController($this->controller);

    $this->restoreUser = new User();
    $this->restoreUser->setAttributes(array(
      'login' => 'restore',
      'password' => '555',
      'confirmPassword' => '555',
      'email' => 'restore@user.ru'
    ));

    $this->restoreUser->save();

    $_SESSION = array();
  }

  public function testRenderRegistrationForm()
  {
    ob_start();
    $this->controller->run('registration');
    $html = ob_get_clean();

    $this->assertContains('name="User[login]"', $html);
    $this->assertContains('name="User[email]"', $html);
    $this->assertContains('name="User[password]"', $html);
    $this->assertContains('name="User[confirmPassword]"', $html);
    $this->assertContains('name="UserProfile[name]"', $html);
  }

  public function testRenderRestoreForm()
  {
    ob_start();
    $this->controller->run('restore');
    $html = ob_get_clean();

    $this->assertContains('RestorePassword[email]', $html);
  }

  public function testRenderLoginForm()
  {
    ob_start();
    $this->controller->run('login');
    $html = ob_get_clean();

    $this->assertContains('Login[login]', $html);
    $this->assertContains('Login[password]', $html);
  }

  /**
   * @expectedException TRedirectException
   * @expectedExceptionMessage Location: /
   */
  public function testLogin()
  {
    Yii::app()->request->setAjax(array('Login' => array('login' => 'restore', 'password' => '555')));
    $this->controller->run('login');
  }

  /**
   * @expectedException TEndException
   */
  public function testLoginFailed()
  {
    Yii::app()->request->setAjax(array('Login' => array('login' => 'restore', 'password' => 'bad password')));
    $this->setOutputCallback(function($data) {
      $validateErrors = json_decode(Arr::get(json_decode($data, true), 'validateErrors'), true);
      $this->assertEquals(array('Login_error' => array('Ошибка неверный логин/пароль!')), $validateErrors);
    });
    $this->controller->run('login');
  }

  /**
   * @expectedException TRedirectException
   * @expectedExceptionMessage Location: test_logout
   */
  public function testLogout()
  {
    $loginModel = new Login();
    $loginModel->setAttributes(array('login' => 'restore', 'password' => '555'));
    $loginModel->authenticate(array(), array());

    $this->assertFalse(Yii::app()->user->isGuest);

    Yii::app()->user->returnUrl = 'test_logout';
    $this->setOutputCallback(function($data) {
      $this->assertTrue(Yii::app()->user->isGuest);
    });
    $this->controller->run('logout');
  }

  /**
   * @expectedException TEndException
   */
  public function testRegistration()
  {
    Yii::app()->request->setAjax(array(
      'User' => array(
        'login' => 'registration',
        'password' => '123',
        'confirmPassword' => '123',
        'email' => 'test@registration.ru'
      ),
      'UserProfile' => array(
        'name' => 'Test'
      )
    ));

    $this->assertNull(User::model()->findByAttributes(array('login' => 'registration')));
    $this->setOutputCallback(function($data) {
      return Arr::get(CJSON::decode($data), 'messageForm');
    });
    $this->expectOutputRegex("/Регистрация успешно завершена./");
    $this->controller->run('registration');
  }

  /**
   * @expectedException TEndException
   */
  public function testRestorePassword()
  {
    Yii::app()->request->setAjax(array('RestorePassword' => array('email' => 'restore@user.ru')));
    $this->setOutputCallback(function($data) {
      return Arr::get(CJSON::decode($data), 'messageForm');
    });

    $this->expectOutputRegex("/Вам на E-mail отправлены дальнейшие инструкции/");
    $this->controller->run('restore');
  }

  /**
   * @expectedException TRedirectException
   * @expectedExceptionMessage Location: /user/restore/
   */
  public function testRestoreConfirmedWithBadCode()
  {
    $this->restoreUser->restore_code = 'restorecode';
    $this->restoreUser->save(false);

    $_GET['code'] = 'bad code';
    $_SESSION = array();
    $this->controller->run('restoreConfirmed');
  }

  public function testRestoreConfirmed()
  {
    $this->restoreUser->restore_code = 'restorecode';
    $this->restoreUser->save(false);

    ob_start();
    $_GET['code'] = 'restorecode';
    $_SESSION = array();
    $this->controller->run('restoreConfirmed');
    $html = ob_get_clean();

    $this->assertContains('Новый пароль выслан на ваш E-mail.', $html);
  }

  public function tearDown()
  {
    User::model()->deleteAllByAttributes(array('login' => 'restore'));
    User::model()->deleteAllByAttributes(array('login' => 'test'));
    User::model()->deleteAllByAttributes(array('login' => 'registration'));
  }
}