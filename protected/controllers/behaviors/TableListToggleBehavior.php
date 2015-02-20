<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 *
 * Подключение:
 * public function behaviors()
 * {
 *   return CMap::mergeArray(parent::behaviors(), array(
 *     'tableListToggleBehavior' => array('class' => 'TableListToggleBehavior')
 *   ));
 * }
 */
class TableListToggleBehavior extends SBehavior
{
  public function isTable()
  {
    return !empty($_COOKIE['lineView']) ? false : true;
  }

  public function getViewTemplate()
  {
    return $this->isTable() ? '_product_block' : '_product_line';
  }
}