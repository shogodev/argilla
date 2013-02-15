<?php

Yii::import('backend.modules.rbac.models.*');
/**
 * @package RBAC
 * @date 04.09.2012
 * @author Nikita Melnikov <melnikov@shogo.ru>
 */
class RoleTest extends CTestCase
{
  public function testAttributelabels()
  {
    $role = new RbacRole();

    $labels = array('title'       => 'Название',
                    'name'        => 'Системное имя',
                    'description' => 'Описание',
                    'bizrule'     => 'Бизнес-логика',
                    'data'        => 'Данные',
                    'tasks'       => 'Задачи');

    $this->assertEquals($role->attributeLabels(), $labels);
  }

  public function testCreate()
  {
    $role = new RbacRole();
    $role->name = 'testRoleTest' . rand(time(), time() + 36000);
    $this->assertEquals($role->save(false), true);

    $id = $role->name;

    unset($role);

    $role = RbacRole::model()->findByPk($id);

    $this->assertNotEmpty($role);
  }

  public function testUpdate()
  {
    $description = 'testTest';

    $role              = new RbacRole();
    $role->name        = 'testTestTest' . uniqid();
    $role->description = $description;
    $role->save(false);

    $id = $role->name;

    unset($role);

    $role = RbacRole::model()->findByPk($id);

    $this->assertEquals($role->description, $description);

    $description = 'newTestTest';

    $role->description = $description;
    $this->assertTrue($role->save(false));

    unset($role);

    $role = RbacRole::model()->findByPk($id);

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

    $roles = RbacRole::getRoles();

    $this->assertEquals($data, $data);
  }

  public function testSetTasks()
  {
    $task = Yii::app()->authManager->createTask('task' . uniqid());

    $role = new RbacRole();
    $name = $role->name  = 'role' . uniqid();
    $role->save(false);

    $role->tasks = array("$task->name" => $task->name);

    unset($role);

    $role = RbacRole::model()->findByPk($name);

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

    $role = new RbacRole();
    $role->name = 'role' . uniqid();
    $role->save(false);

    $role->tasks = $tasks;

    $this->assertEquals($tasks, $role->getTasks());

    $role->clearTasks();

    $this->assertEquals(array(), $role->tasks);
  }

}