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
  public $attribute;

  protected function getIframeUrl($data)
  {
    if( $data->{$this->attribute})
    {
      return Yii::app()->controller->createUrl($this->iframeAction, array('id' => $data->{$this->attribute}, 'popup' => true));
    }
    else
    {
      return null;
    }
  }
}