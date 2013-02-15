<?php

Yii::import('backend.tests.unit.modules.directory.common.TestDirectory');
Yii::import('backend.modules.directory.behaviors.*');

/**
 * Так как моделей справочников может не быть, для тестов необходимо создавать таблицу,
 * котоаря будет относиться к TestDirectory
 *
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @date 24.10.12
 * @package Directory
 */
class DirectoryTestCase extends CDbTestCase
{
  /**
   * like fixtures
   *
   * @var array
   */
  protected $values = array(
    array('id' => 1, 'name' => 'user1'),
    array('id' => 2, 'name' => 'user2'),
    array('id' => 3, 'name' => 'user3'),
    array('id' => 4, 'name' => 'user4'),
  );

  /**
   * Удаление и последующее создание таблицы, указанной в $this->getTableName
   * и завполнение её значениями из $this->values
   *
   * @return boolean
   */
  protected function setUp()
  {
    $command = Yii::app()->db->createCommand();

    try
    {
      $command->dropTable($this->getTableName());
    }
    catch( Exception $e ){}

    $command->createTable($this->getTableName(), array(
                        'id'   => 'pk',
                        'name' => 'VARCHAR(255) NOT NULL',
                        'visible' => 'INT(1) DEFAULT 1 NOT NULL',
                      ));

    $this->insertValues();
    
    return parent::setUp();
  }

  /**
   * После каждого теста таблица удаляется
   */
  public function tearDown()
  {
    try
    {
      $command = Yii::app()->db->createCommand();
      $command->dropTable($this->getTableName());
    }
    catch( Exception $e ){}

    parent::tearDown();
  }

  /**
   * Прикрепляем поведение справочника
   *
   * @param CActiveRecord $model
   *
   * @return void
   */
  public function addDirectory(&$model)
  {
    $model->attachBehavior('test', new DirectoryBehavior);
    $model->test->field = 'section_id';
    $model->test->model = new TestDirectory();
    $model->test->type = 'select';
    $model->test->init();
  }

  /**
   * Получение имени таблицы из тестового класса
   *
   * @return string
   */
  protected function getTableName()
  {
    return TestDirectory::$tableName;
  }

  /**
   * Заполнение таблицы значениями из $this->values
   *
   * @return void
   */
  protected function insertValues()
  {
    if( empty($this->values) )
      return;

    foreach( $this->values as $value )
    {
      Yii::app()->db->createCommand()->insert($this->getTableName(), $value);
    }
  }
}