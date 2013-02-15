<?php

Yii::import('backend.modules.rbac.models.*');
/**
 * @package RBAC
 * @date 04.09.2012
 * @author Nikita Melnikov <melnikov@shogo.ru>
 */
class UserTest extends CTestCase
{
  public function testCreate()
  {
    $model = new BUser();
    $model->username = 'test';
    $model->password = 'test';
    $model->save(false);

    $id = $model->id;
    unset($model);

    $model = BUser::model()->findByPk($id);

    $this->assertInstanceOf('User', $model);
  }

  public function testEdit()
  {
    $model = $this->createUser();

    $oldName     = $model->username;
    $oldPassword = $model->password;

    $model->username = rand(0, 10000);
    $model->save(false);

    $id = $model->id;

    unset($model);

    $model = BUser::model()->findByPk($id);

    $newName     = $model->username;
    $newPassword = $model->password;

    $this->assertNotEquals($newName, $oldName);
    $this->assertEquals($newPassword, $oldPassword);
  }

  public function testEditPasswordNew()
  {
    $model = $this->createUser();

    $oldPassword = $model->password;

    $model->password = rand(0, 5435);
    $model->save(false);

    $id = $model->id;

    unset($model);

    $model = BUser::model()->findByPk($id);

    $this->assertNotEquals($oldPassword, $model->password);
  }

  public function testGetRolesEmpty()
  {
    $user = $this->createUser();
    $this->assertEmpty($user->roles);
  }

  public function testGetRolesNotEmpty()
  {
    $user = $this->createUser();

    $roles = array();
    for( $i = 0; $i < 3; $i++ )
    {
      $role = 'role' . uniqid();

      Yii::app()->authManager->createRole($role);

      $roles[$role] = $role;
    }

    $user->roles = $roles;

    $id = $user->id;

    unset($user);

    $user = BUser::model()->findByPk($id);

    $this->assertEquals($roles, $user->roles);

  }

  public function testClearRoles()
  {
    $user = $this->createUser();

    $roles = array();
    for( $i = 0; $i < 3; $i++ )
    {
      $role = 'role' . uniqid();

      Yii::app()->authManager->createRole($role);

      $roles[$role] = $role;
    }

    $user->roles = $roles;

    $this->assertEquals($user->roles, $roles);

    $user->clearRoles();

    $this->assertEquals(array(), $user->roles);
  }

  public function testAttributesLabel()
  {
    $attributes = array('username' => 'Имя пользователя',
                        'password' => 'Пароль',
                        'roles'    => 'Роли',
                        'passwordNew' => 'Пароль',
                       );

    $user = new BUser();

    $this->assertEquals($user->attributeLabels(), $attributes);
  }

  public function testValidation()
  {
    $user = new BUser();
    $user->username . uniqid();

    $this->assertFalse($user->save());

    $user->username = 'username1234567890123612638321387126387612837' . uniqid() . uniqid();
    $user->password = '123' . uniqid();
    $this->assertFalse($user->save());

    $user->username = 'user' . uniqid();
    $this->assertTrue($user->save());
  }

  private function createUser()
  {
    $user = new BUser;
    $user->username = 'user' . uniqid();
    $user->password = '123';
    $user->save(false);

    return $user;
  }
}