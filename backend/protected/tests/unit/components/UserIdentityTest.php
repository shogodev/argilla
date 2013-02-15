<?php

Yii::import('backend.components.BUserIdentity');
Yii::import('backend.modules.rbac.models.User');

class UserIdentityTest extends CTestCase
{
  public function testCreatePassoword()
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
    $user->password = 'test';
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