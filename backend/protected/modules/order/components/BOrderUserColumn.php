<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.order.components
 */
class BOrderUserColumn extends BOrderPopupColumn
{
  protected function getIframeUrl($data)
  {
    if( $data->{$this->name} )
    {
      return Yii::app()->controller->createUrl($this->iframeAction, array('id' => $data->{$this->name}, 'popup' => true));
    }
    else
    {
      return null;
    }
  }
}