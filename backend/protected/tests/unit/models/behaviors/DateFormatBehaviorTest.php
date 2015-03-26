<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.tests.models.behaviors
 */
Yii::import('backend.tests.components.TDateFormatTestModel');

class DateFormatBehaviorTest extends CTestCase
{
  public function setUp()
  {
    $table = TDateFormatTestModel::STATIC_TABLE_NAME;

    $command = Yii::app()->db->createCommand();
    $command->createTable($table, array('id' => 'pk', 'date' => 'date NOT NULL', 'date_time' => 'datetime NOT NULL', 'timestamp' => 'timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP'), 'ENGINE=InnoDB');

    $command->insert($table, array('id' => 1, 'timestamp' => '0000-00-00 00:00:00'));
    $command->insert($table, array('id' => 2, 'date' => '2013-08-15', 'date_time' => '2014-03-18 10:34:11', 'timestamp' => '2015-03-18 15:00:12'));
  }

  public function testAfterFind()
  {
    $model = TDateFormatTestModel::model()->findByPk(1);
    $this->assertEquals('', $model->date);
    $this->assertEquals('', $model->date_time);
    $this->assertEquals('', $model->timestamp);

    $model = TDateFormatTestModel::model()->findByPk(2);
    $this->assertEquals('15.08.2013', $model->date);
    $this->assertEquals('18.03.2014 10:34:11', $model->date_time);
    $this->assertEquals('18.03.2015 15:00:12', $model->timestamp);
  }

  public function testBeforeSaveTypeDate()
  {
    $model = new TDateFormatTestModel;
    $model->id = 3;
    $model->save();
    $model = TDateFormatTestModel::model()->findByPk(3);
    $this->assertEquals('', $model->date);

    $model = new TDateFormatTestModel;
    $model->id = 4;
    $model->date = '04.05.2007';
    $model->save();
    $model = TDateFormatTestModel::model()->findByPk(4);
    $this->assertEquals('04.05.2007', $model->date);

    $model = new TDateFormatTestModel;
    $model->detachBehavior('dateFormatBehavior');
    $model->attachBehavior('dateFormatBehavior', array(
      'class' => 'DateFormatBehavior',
      'attribute' => 'date',
      'defaultNow' => true
    ));
    $model->id = 5;
    $model->save();
    $model = TDateFormatTestModel::model()->findByPk(5);
    $this->assertEquals(date('d.m.Y'), $model->date);
  }

  public function testBeforeSaveTypeDateTime()
  {
    $model = new TDateFormatTestModel;
    $model->id = 6;
    $model->save();
    $model = TDateFormatTestModel::model()->findByPk(6);
    $this->assertEquals('', $model->date_time);

    $model = new TDateFormatTestModel;
    $model->id = 7;
    $model->date_time = '04.05.2007 18:30:34';
    $model->save();
    $model = TDateFormatTestModel::model()->findByPk(7);
    $this->assertEquals('04.05.2007 18:30:34', $model->date_time);

    $model = new TDateFormatTestModel;
    $model->detachBehavior('dateFormatBehavior2');
    $model->attachBehavior('dateFormatBehavior2', array(
      'class' => 'DateFormatBehavior',
      'attribute' => 'date_time',
      'defaultNow' => true
    ));
    $model->id = 8;
    $model->save();
    $model = TDateFormatTestModel::model()->findByPk(8);
    $this->assertEquals(date('d.m.Y H:i:s'), $model->date_time);
  }

  public function testBeforeSaveTypeTimestamp()
  {
    $model = new TDateFormatTestModel;
    $model->id = 9;
    $model->save();

    $model = TDateFormatTestModel::model()->findByPk(9);
    $this->assertNotEmpty($model->timestamp);

    $model = new TDateFormatTestModel;
    $model->id = 10;
    $model->timestamp = '04.05.2008 18:30:34';
    $model->save();
    $model = TDateFormatTestModel::model()->findByPk(10);
    $this->assertEquals('04.05.2008 18:30:34', $model->timestamp);

    $model = new TDateFormatTestModel;
    $model->detachBehavior('dateFormatBehavior3');
    $model->attachBehavior('dateFormatBehavior3', array(
      'class' => 'DateFormatBehavior',
      'attribute' => 'timestamp',
      'defaultNow' => true
    ));
    $model->id = 11;
    $model->save();
    $model = TDateFormatTestModel::model()->findByPk(11);
    $this->assertEquals(date('d.m.Y H:i:s'), $model->timestamp);
  }

  public function tearDown()
  {
    Yii::app()->db->createCommand()->dropTable(TDateFormatTestModel::STATIC_TABLE_NAME);
  }
}