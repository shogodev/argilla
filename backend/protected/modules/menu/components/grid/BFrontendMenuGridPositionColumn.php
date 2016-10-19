<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.menu.components.grid
 */
class BFrontendMenuGridPositionColumn extends OnFlyEditField
{
  /**
   * @param int $row
   * @param BFrontendMenuGridAdapter $data
   */
  protected function renderDataCellContent($row, $data)
  {
    if( $data->active )
    {
      parent::renderDataCellContent($row, $data);
    }
  }

  /**
   * @param BFrontendMenuGridAdapter $data
   *
   * @return mixed
   */
  protected function getPrimaryKey($data)
  {
    return BFrontendMenuGridView::encodeMenuItem($data);
  }
}