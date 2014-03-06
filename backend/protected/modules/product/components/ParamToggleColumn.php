<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.product.components
 */

Yii::import('zii.widgets.grid.CGridColumn');

class ParamToggleColumn extends JToggleColumn
{
  protected function renderButton($button, $row, $data)
  {
    if( $data->isGroup() )
      return;

    parent::renderButton($button, $row, $data);
  }
}