<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 */
Yii::import('backend.components.auth.*');
Yii::import('backend.modules.rbac.models.User');

class UserIdentityTest extends CTestCase
{
  public function testCreatePassword()
  {
    $salt = 'test';

    $username = 'test';
    $password = 'test';

    $this->assertEquals(md5($username.$password.$salt), BUserIdentity::createPassword($username, $password));
  }

  public function testUserPassword()
  {
    $user = new BUser();
    $user->username = 'test';
    $user->passwordNew = 'test';
    $user->save(false);

    $this->assertEquals(BUserIdentity::createPassword('test', 'test'), $user->password);
  }

  public function testDevLogin()
  {
    $identity = new BUserIdentity('admin', '123');
    $this->assertEquals($identity->authenticate(), true);
    $this->assertEquals($identity->id, 1);
  }
}