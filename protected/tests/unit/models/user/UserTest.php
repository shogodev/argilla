<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package 
 */
class UserTest extends CDbTestCase
{
  /**
   * @var User $user
   */
  public $user;

  public function setUp()
  {
    $this->user = new User();
    $this->user->setAttributes(array(
      'login' => 'test',
      'password' => '123',
      'confirmPassword' => '123',
      'email' => 'test@shogo.ru'
    ));
    $this->user->save();
  }

  public function testRegistration()
  {
    $login = '  TesTRegisterРуСскийЁ ';
    $user = new User();
    $user->setAttributes(array(
      'login' => $login,
      'password' => '123',
      'confirmPassword' => '123',
      'email' => 'testRegister@shogo.ru'
    ));
    $user->save();

    $loginLowerCase = 'testregisterрусскийё';
    /**
     * @var User $model
     */
    $model = User::model()->findByAttributes(array('login' => $loginLowerCase));
    $this->assertEquals('testregister@shogo.ru', $model->email);
    $this->assertEquals(FUserIdentity::createPassword($loginLowerCase, '123'), $model->password_hash);
  }

  public function testChangePasswordWithEmptyOldPassword()
  {
    $userChangePassword = User::model()->findByAttributes(array('login' => 'test'));
    $userChangePassword->attributes = array(
      'password' => '12345',
      'confirmPassword' => '12345'
    );
    $userChangePassword->save();

    $this->assertContains('Необходимо заполнить поле «Старый пароль».', $userChangePassword->errors['oldPassword']);
    $this->assertContains('Не правильно введен Старый пароль', $userChangePassword->errors['oldPassword']);

  }

  public function testChangePasswordWithBadConfirmPassword()
  {
    $userChangePassword = User::model()->findByAttributes(array('login' => 'test'));
    $userChangePassword->attributes = array(
      'oldPassword' => '123',
      'password' => '12345',
      'confirmPassword' => '111'
    );

    $userChangePassword->save();

    $this->assertContains('Поля "Новый пароль" и "Подтверждение пароля" не совпадают', $userChangePassword->errors['confirmPassword']);
  }

  public function testChangePasswordWithBadOldPassword()
  {
    $userChangePassword = User::model()->findByAttributes(array('login' => 'test'));
    $userChangePassword->attributes = array(
      'oldPassword' => '555',
      'password' => '12345',
      'confirmPassword' => '12345'
    );

    $userChangePassword->save();

    $this->assertContains('Не правильно введен Старый пароль', $userChangePassword->errors['oldPassword']);
  }

  public function testChangePassword()
  {
    $userChangePassword = User::model()->findByAttributes(array('login' => 'test'));
    $userChangePassword->attributes = array(
      'oldPassword' => '123',
      'password' => '12345',
      'confirmPassword' => '12345'
    );
    $userChangePassword->save();

    /**
     * @var User $model
     */
    $model = User::model()->findByAttributes(array('login' => 'test'));
    $this->assertEquals(FUserIdentity::createPassword('test', '12345'), $model->password_hash);
  }

  public function testGenerateRestoreCodeWithEmptyEmail()
  {
    $restore = new RestorePassword(RestorePassword::GENERATE_RESTORE_CODE);
    $restore->attributes = array(
      'email' => ''
    );

    $this->assertFalse($restore->validate());
    $this->assertContains('Необходимо заполнить поле «Email».', $restore->errors['email']);

    $this->user->refresh();
    $this->assertEmpty($this->user->restore_code);
  }

  public function testGenerateRestoreCode()
  {
    $restore = new RestorePassword(RestorePassword::GENERATE_RESTORE_CODE);
    $restore->attributes = array(
      'email' => $this->user->email
    );

    $this->assertTrue($restore->validate());
    $this->user->refresh();
    $this->assertNotEmpty($this->user->restore_code);
  }

  public function testGenerateNewPassword()
  {
    $this->user->restore_code = 'restorecode';
    $this->user->save(false);
    $passwordHash = $this->user->password_hash;

    $restore = new RestorePassword(RestorePassword::GENERATE_NEW_PASSWORD);
    $restore->attributes = array(
      'restoreCode' => $this->user->restore_code
    );

    $this->assertTrue($restore->validate());
    $this->user->refresh();

    $this->assertEmpty($this->user->restore_code);
    $this->assertNotEquals($passwordHash, $this->user->password_hash);
  }

  public function tearDown()
  {
    User::model()->deleteAllByAttributes(array('login' => 'test'));
    User::model()->deleteAllByAttributes(array('login' => 'testregisterрусскийё'));
  }
}