<?php
class SNotificationTest extends CDbTestCase
{
  protected $fixtures = array(
    'notification' => 'SNotification',
  );

  public function setUp()
  {
    Yii::app()->setUnitEnvironment('Info', 'index');
    Yii::app()->email->delivery = 'debug';

    parent::setUp();
  }

  public function testSend()
  {
    Yii::app()->notification->send('testSend');
    $mail = Yii::app()->user->getFlash('email');
    $this->assertContains('admin@test.ru', $mail);

    Yii::app()->notification->send('testSend', array(), 'user@test.ru');
    $mail = Yii::app()->user->getFlash('email');
    $this->assertContains('user@test.ru', $mail);
  }

  public function testRegister()
  {
    Yii::app()->notification->send('testRegister');

    $model = SNotification::model()->findByAttributes(array('index' => 'testRegister'));
    $this->assertInstanceOf('SNotification', $model);
  }
}