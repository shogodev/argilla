<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.product
 */
Yii::import('zii.widgets.grid.CGridColumn');

class ParamToggleColumn extends JToggleColumn
{
  protected function renderButton($button, $row, $data)
  {
    if( $this->name == 'visible' || $data->isGroup() )
      parent::renderButton($button, $row, $data);
  }
}