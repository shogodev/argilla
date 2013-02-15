<?php
/**
 * @package RBAC
 * @date 04.09.2012
 * @author Nikita Melnikov <melnikov@shogo.ru>
 */
class TaskTest extends CTestCase
{
  public function testFindAll()
  {
    $criteria = new CDbCriteria();
    $criteria->condition = 'type=:type';
    $criteria->params    = array(':type' => CAuthItem::TYPE_TASK);

    $tasks = RbacRole::model()->findAll($criteria);

    $authTasks = Yii::app()->authManager->getTasks();

    $this->assertEquals(count($tasks), count($authTasks));
    $this->assertEquals(count(RbacTask::getTasks()), count($tasks));
  }

  public function testGetOperations()
  {
    $task = new RbacTask();
    $task->name = 'task' . uniqid();
    $task->save(false);

    $this->assertEmpty($task->operations);

    $operations = array();
    for( $i = 0; $i < 3; $i++ )
    {
      $operation = new BRbacOperation();
      $operation->name = 'operation' . uniqid();
      $operation->save(false);

      $operations[$operation->name] = $operation->name;
    }

    $task->operations = $operations;

    $this->assertEquals($task->operations, $operations);
  }

  public function testClearOperations()
  {
    $task = new RbacTask();
    $task->name = 'task' . uniqid();
    $task->save(false);

    $operations = array();

    for( $i = 0; $i < 3; $i++ )
    {
      $operation = 'operation' . uniqid();
      $operations[$operation] = $operation;

      Yii::app()->authManager->createOperation($operation);
    }

    $task->operations = $operations;

    $this->assertEquals($task->operations, $operations);

    $task->clearOperations();

    $this->assertEmpty($task->operations);

  }
}