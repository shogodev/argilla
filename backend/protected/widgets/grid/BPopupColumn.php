<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.widgets.BPopupColumn
 */
class BPopupColumn extends BDataColumn
{
  public $filter = false;

  public $widget = 'BAssociationButton';

  public $iframeAction;

  public $htmlOptions = array('class' => 'button-column');

  public $parameters = array();

  public $closeOperation;

  protected function renderDataCellContent($row, $data)
  {
    if( $this->closeOperation === null )
      $this->closeOperation = '$.fn.yiiGridView.update("'.$this->grid->id.'");';

    Yii::app()->controller->widget($this->widget, array(
      'name' => $this->name,
      'parameters' => $this->parameters,
      'iframeAction' => $this->iframeAction,
      'model' => $data,
      'assignerOptions' => array(
        'closeOperation' => new CJavaScriptExpression('function(){'.$this->closeOperation.'}'),
      ),
    ));
  }
}