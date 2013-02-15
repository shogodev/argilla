<?php

/**
 * @package RBAC
 * @date 04.09.2012
 * @author Nikita Melnikov <melnikov@shogo.ru>
 */
class OperationTest extends CTestCase
{
  public function testGetOperations()
  {
    $criteria = new CDbCriteria();
    $criteria->condition = 'type=:type';
    $criteria->params = array(':type' => CAuthItem::TYPE_OPERATION);

    $data = BRbacOperation::model()->findAll($criteria);

    $this->assertEquals(count($data), count(BRbacOperation::getOperations()));
  }
}