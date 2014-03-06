<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 */
Yii::import('backend.modules.menu.controllers.*');
Yii::import('backend.modules.menu.models.*');
Yii::import('backend.modules.menu.components.*');

abstract class MenuModuleTest extends CDbTestCase
{
  protected function tearDown()
  {
    foreach($this->getTables() as $table)
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