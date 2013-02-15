<?php
/**
 * User: Nikita Melnikov <melnikov@shogo.ru>
 * Date: 12/12/12
 */

Yii::import('backend.modules.menu.controllers.*');
Yii::import('backend.modules.menu.models.*');
Yii::import('backend.modules.menu.components.*');

abstract class MenuModuleTest extends CDbTestCase
{
  public $fixtures = array(

  );

  protected function setUp()
  {
    parent::setUp();
  }

  protected function tearDown()
  {
    foreach( $this->getTables() as $table )
    {
      $this->getFixtureManager()->truncateTable($table);
    }
  }

  protected function getTables()
  {
    $data = array();

    $data[] = BFrontendMenu::model()->tableName();
    $data[] = BFrontendMenuItem::model()->tableName();
    $data[] = BFrontendCustomMenuItem::model()->tableName();
    $data[] = BFrontendCustomMenuItemData::model()->tableName();

    return $data;
  }
}