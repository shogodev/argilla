<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 */
Yii::import('system.test.CDbFixtureManager');

class SFixtureManager extends CDbFixtureManager
{
  public function init()
  {
    $this->basePath = Yii::getPathOfAlias($this->basePath ? $this->basePath : 'backend.tests.fixtures');
    parent::init();
  }

  public function loadFixture($tableName)
  {
    return parent::loadFixture($this->setPrefix($tableName));
  }

  public function truncateTable($tableName)
  {
    parent::truncateTable($this->setPrefix($tableName));
  }

  protected function setPrefix($tableName)
  {
    if( ($prefix = $this->getDbConnection()->tablePrefix) !== null )
      $tableName = preg_replace('/^'.$prefix.'(\w+)/', '{{$1}}', $tableName);

    return $tableName;
  }
}