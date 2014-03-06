<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.menu.components.grid
 */
class BFrontendMenuGridTypeColumn extends BDataColumn
{
  /**
   * @param int $row
   * @param BFrontendMenuGridAdapter $data
   */
  protected function renderDataCellContent($row, $data)
  {
    if( $data->isCustom )
    {
      echo CHtml::link($data->getType(), '#', array('data-model-id' => $data->model->getId(), 'class'   => 'custom-item edit-custom-item'));

      /**
       * @var BFrontendCustomMenuItem $customItem
       */
      $customItem = $data->model;

      $listData = '';
      foreach( $customItem->data as $entry )
      {
        $listData .= CHtml::tag('li', array(), $entry->name.' - '.$entry->value);
      }
      echo CHtml::tag('ul', array(), $listData);
    }
    else
      echo $data[$this->name];
  }
}