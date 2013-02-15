<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.widgets.grid.BButtonColumn
 */
Yii::import('bootstrap.widgets.TbButtonColumn');

class BButtonColumn extends TbButtonColumn
{
  public $template = '{update} {delete}';

  public $header   = 'Действия';

  protected function renderFilterCellContent()
  {
    return;
  }
}