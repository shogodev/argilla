<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.widgets.grid
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

    $widgetOptions = array(
      'name' => $this->name,
      'model' => $data,
      'assignerOptions' => array(
        'closeOperation' => new CJavaScriptExpression('function(){'.$this->closeOperation.'}'),
      ),
    );

    if( !empty($this->parameters) )
      $widgetOptions['parameters'] = $this->parameters;
    if( !empty($this->iframeAction) )
      $widgetOptions['iframeAction'] = $this->iframeAction;

    Yii::app()->controller->widget($this->widget, $widgetOptions);
  }
}