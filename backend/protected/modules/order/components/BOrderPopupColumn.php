<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.order.components
 */
class BOrderPopupColumn extends BDataColumn
{
  public $htmlOptions = array('class' => 'span2');

  public $iframeAction;

  protected function renderDataCellContent($row, $data)
  {
    $value     = $this->getValue($row, $data);
    $iframeUrl = $this->getIframeUrl($data);

    if( $iframeUrl )
    {
      echo CHtml::tag('a', array(
        'class' => $this->name.'_popup',
        'href' => '#',
        'data-iframeurl' => $iframeUrl,
      ), $value, true);

      $this->registerPopupScript();
    }
    else
      echo $value;
  }

  protected function getValue($row, $data)
  {
    ob_start();
    parent::renderDataCellContent($row, $data);
    return ob_get_clean();
  }

  protected function getIframeUrl($data)
  {
    return Yii::app()->controller->createUrl($this->iframeAction, array('id' => $data->id, 'popup' => true));
  }

  protected function registerPopupScript()
  {
    Yii::app()->clientScript->registerScript($this->name.'ColumnClick', "
      $('body').on('click', '.items a.{$this->name}_popup', function(e){
        e.preventDefault();
        assigner.apply(this, {});
      });
    ", CClientScript::POS_READY);
  }
}