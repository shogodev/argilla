<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
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
    $command->createTable($table, array('id' => 'pk', 'date' => 'date NOT NULL'), 'ENGINE=InnoDB');

    $command->insert($table, array('id' => 1));
    $command->insert($table, array('id' => 2, 'date' => '2013-08-15'));
  }

  public function testAfterFind()
  {
    $model = TDateFormatTestModel::model()->findByPk(1);
    $this->assertEquals('', $model->date);

    $model = TDateFormatTestModel::model()->findByPk(2);
    $this->assertEquals('15.08.2013', $model->date);
  }

  public function testBeforeSave()
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
    $model->attachBehavior('dateFormatBehaviorNow', array(
      'class' => 'DateFormatBehavior',
      'attribute' => 'date',
      'defaultNow' => true
    ));
    $model->id = 5;
    $model->save();
    $model = TDateFormatTestModel::model()->findByPk(5);
    $this->assertEquals(date('d.m.Y'), $model->date);
  }

  public function tearDown()
  {
    Yii::app()->db->createCommand()->dropTable(TDateFormatTestModel::STATIC_TABLE_NAME);
  }
}