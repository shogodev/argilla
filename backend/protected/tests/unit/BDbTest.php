<?php
class BDbTest extends CTestCase
{
  public function testConnection()
  {
    $this->assertNotEquals(null, Yii::app()->db);
  }
}