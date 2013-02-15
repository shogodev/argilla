<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 */
Yii::import('system.test.CDbFixtureManager');

class BFixtureManager extends CDbFixtureManager
{
  public function init()
  {
    $this->basePath = Yii::getPathOfAlias('backend.tests.fixtures');
    parent::init();
  }

  public function loadFixture($tableName)
  {
    if( ($prefix = $this->getDbConnection()->tablePrefix) !== null )
      $tableName = preg_replace('/^'.$prefix.'(\w+)/', '{{$1}}', $tableName);

    return parent::loadFixture($tableName);
  }

  public function truncateTable($tableName)
  {
    if( ($prefix = $this->getDbConnection()->tablePrefix) !== null )
      $tableName = preg_replace('/^'.$prefix.'(\w+)/', '{{$1}}', $tableName);

    parent::truncateTable($tableName);
  }
}