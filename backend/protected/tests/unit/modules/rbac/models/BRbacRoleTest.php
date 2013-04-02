<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 */
Yii::import('backend.modules.rbac.models.*');

class BRbacRoleTest extends CTestCase
{
  public function testCreate()
  {
    $role = new BRbacRole();
    $role->name = 'testRoleTest' . rand(time(), time() + 36000);
    $this->assertEquals($role->save(false), true);

    $id = $role->name;

    unset($role);

    $role = BRbacRole::model()->findByPk($id);

    $this->assertNotEmpty($role);
  }

  public function testUpdate()
  {
    $description = 'testTest';

    $role              = new BRbacRole();
    $role->name        = 'testTestTest' . uniqid();
    $role->description = $description;
    $role->save(false);

    $id = $role->name;

    unset($role);

    $role = BRbacRole::model()->findByPk($id);

    $this->assertEquals($role->description, $description);

    $description = 'newTestTest';

    $role->description = $description;
    $this->assertTrue($role->save(false));

    unset($role);

    $role = BRbacRole::model()->findByPk($id);

    $this->assertEquals($role->description, $description);
  }

  public function testGetRoles()
  {
    $auth = Yii::app()->authManager;

    $data = array();
    foreach( $auth->getRoles() as $role )
    {
      $data[$role->name] = $role->name;
    }

    $roles = BRbacRole::getRoles();

    $this->assertEquals($data, $data);
  }

  public function testSetTasks()
  {
    $task = Yii::app()->authManager->createTask('task' . uniqid());

    $role = new BRbacRole();
    $name = $role->name  = 'role' . uniqid();
    $role->save(false);

    $role->tasks = array("$task->name" => $task->name);

    unset($role);

    $role = BRbacRole::model()->findByPk($name);

    $this->assertEquals($role->tasks, array($task->name => $task->name));
  }

  public function testClearTasks()
  {
    $authManager = Yii::app()->authManager;

    $tasks = array();
    for( $i = 0; $i < 10; $i++ )
    {
      $task = $authManager->createTask('task' . uniqid());
      $tasks[$task->name] = $task->name;
    }

    $role = new BRbacRole();
    $role->name = 'role' . uniqid();
    $role->save(false);

    $role->tasks = $tasks;

    $this->assertEquals($tasks, $role->getTasks());

    $role->clearTasks();

    $this->assertEquals(array(), $role->tasks);
  }

}