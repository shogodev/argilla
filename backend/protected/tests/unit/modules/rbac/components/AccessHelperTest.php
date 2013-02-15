<?php

Yii::import('backend.modules.rbac.*');
Yii::import('backend.modules.rbac.components.*');
Yii::import('backend.modules.rbac.models.*');
Yii::import('backend.modules.rbac.controllers.*');

/**
 * @package RBAC
 * @date 04.09.2012
 * @author Nikita Melnikov <melnikov@shogo.ru>
 */
class AccessHelperTest extends CTestCase
{
  public function testAssigment()
  {
    $mockSession = $this->getMock('CHttpSession', array('regenerateID'));
    Yii::app()->setComponent('session', $mockSession);

    $role       = new RbacRole();
    $roleName   = $role->name = 'role' . uniqid();
    $role->save(false);

    $user = $this->login();

    Yii::app()->authManager->assign($roleName, $user->id);

    $this->assertTrue(Yii::app()->user->checkAccess($roleName));
    $this->assertFalse(Yii::app()->user->checkAccess('nether' . uniqid()));
  }

  public function testHierarchyAccess()
  {
    $mockSession = $this->getMock('CHttpSession', array('regenerateID'));
    Yii::app()->setComponent('session', $mockSession);

    $user = $this->login();

    $auth = Yii::app()->authManager;

    $role      = $auth->createRole('role' . uniqid());
    $operation = $auth->createOperation('operation' . uniqid());
    $task      = $auth->createTask('task' . uniqid());

    $randomTask = $auth->createTask('task' . uniqid());


    $task->addChild($operation->name);
    $role->addChild($task->name);

    $auth->assign($role->name, $user->id);

    $this->assertTrue(Yii::app()->user->checkAccess($role->name));
    $this->assertTrue(Yii::app()->user->checkAccess($task->name));
    $this->assertTrue(Yii::app()->user->checkAccess($operation->name));

    $this->assertFalse(Yii::app()->user->checkAccess($randomTask->name));

  }

  public function testCheckAccessTask()
  {
    $this->login();
    $this->setUnitEnvironment('RbacModule', 'RoleController');

    $access = new AccessHelper();

    if( file_exists(BUserIdentity::ALLOW_FREE_AUTH) )
      $this->assertTrue($access->checkAccess());
    else
      $this->assertFalse($access->checkAccess());
  }

  public function testCheckAccessOperation()
  {
    $this->login();
    $this->setUnitEnvironment('RbacModule', 'RoleController');

    $access = new AccessHelper();

    if( file_exists(BUserIdentity::ALLOW_FREE_AUTH) )
      $this->assertTrue($access->checkAccess(true));
    else
      $this->assertFalse($access->checkAccess(true));
  }

  public function testCheckAccessTaskSuccess()
  {
    $user = $this->login();

    $this->setUnitEnvironment('RbacModule', 'RoleController');

    $auth = Yii::app()->authManager;

    $access = new AccessHelper();

    $taskName = $access->getTaskName();
    $auth->assign($taskName, $user->id);

    $this->assertTrue($access->checkAccess());
  }

  public function testCheckAccessOperationSuccess()
  {
    $user = $this->login();

    $auth = Yii::app()->authManager;

    $access = new AccessHelper('rbac', 'role', 'update'.  uniqid());
    $operationName = $access->getOperationName();

    $role = $auth->createRole('role'.  uniqid());
    $operation = $auth->createOperation($operationName);
    Yii::app()->authManager->addItemChild($role->name, $operation->name);
    $auth->assign($role->name, $user->id);

    $this->assertTrue($access->checkAccess(true));
  }

  public function testTaskCreation()
  {
    $action = uniqid();
    $this->setUnitEnvironment('RbacModule', uniqid(), $action);

    $access = new AccessHelper();
    $access->checkAccess();
  }

  public function testOperationCreation()
  {
    $action = uniqid();
    $this->setUnitEnvironment('RbacModule', 'RoleController', $action);

    $access = new AccessHelper();
    $access->checkAccess(true);

    $name = 'RbacModule:RoleController:' . $action;

    $this->assertNotNull(BRbacOperation::model()->findAllByPk($name));
  }

  public function testAccessToRemoteController()
  {
    $user = $this->login();

    $access = new AccessHelper(false, 'role');

    if( file_exists(BUserIdentity::ALLOW_FREE_AUTH) )
      $this->assertTrue($access->checkAccess());
    else
      $this->assertFalse($access->checkAccess());
  }

  public function testStaticCall()
  {
    $this->setUnitEnvironment('RbacModule', 'RoleController');
    $user = $this->login();

    if( file_exists(BUserIdentity::ALLOW_FREE_AUTH) )
      $this->assertTrue(AccessHelper::init()->checkAccess());
    else
      $this->assertFalse(AccessHelper::init()->checkAccess());
  }

  public function testStaticCallSuccess()
  {
    $user = $this->login();

    $module     = 'module';
    $controller = 'ctrl' . uniqid();
    $action     = 'action' . uniqid();

    $role      = Yii::app()->authManager->createRole('role' . uniqid());
    $task      = Yii::app()->authManager->createTask($module . ':' . $controller);
    $operation = Yii::app()->authManager->createOperation($module . ':' . $controller . ':' . $action);

    Yii::app()->authManager->addItemChild($role->name, $task->name);
    Yii::app()->authManager->addItemChild($task->name, $operation->name);
    Yii::app()->authManager->assign($role->name, $user->id);

    $this->asserttrue(AccessHelper::init($module, $controller, $action)->checkAccess());
    $this->asserttrue(AccessHelper::init($module, $controller, $action)->checkAccess(true));
  }

  private function login()
  {
    $user     = new BUser();
    $username = $user->username = 'testTest' . uniqid();
    $password = $user->password = '123';
    $user->save(false);

    $identity = new TUserIdentity($username, $password);
    $identity->authenticate();

    Yii::app()->user->login($identity);

    return $user;
  }

  private function setUnitEnvironment($module, $controller, $action = 'index')
  {
    $controller = new CController($controller, new $module($module, null));
    $controller->setAction(new CInlineAction($controller, $action));

    Yii::app()->setController($controller);
  }
}