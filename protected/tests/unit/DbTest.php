<?php
class DbTest extends CTestCase
{
  public function testConnection()
  {
    $this->assertNotEquals(NULL, Yii::app()->db);
  }
}
?>