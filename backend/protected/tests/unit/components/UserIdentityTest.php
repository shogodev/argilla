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
  public function setUp()
  {
    parent::setUp();

    $user = new BUser();
    $user->id = 10;
    $user->username = 'admin';
    $user->passwordNew = '12345';
    $user->save(false);
  }

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
    $user->id = 11;
    $user->username = 'test';
    $user->passwordNew = 'test';
    $user->save(false);

    $this->assertEquals(BUserIdentity::createPassword('test', 'test'), $user->password);
  }

  public function testDevLogin()
  {
    $identity = new BUserIdentity('admin', '12345');
    $this->assertEquals($identity->authenticate(), true);
    $this->assertEquals($identity->id, 10);
  }

  public function tearDown()
  {
    BUser::model()->deleteByPk(10);
    BUser::model()->deleteByPk(11);
  }

}