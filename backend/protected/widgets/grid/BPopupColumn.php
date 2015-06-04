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

  protected function renderDataCellContent($row, $data)
  {
    $widgetOptions = array(
      'name' => $this->name,
      'model' => $data,
      'assignerOptions' => array(
        'updateGridId' => $this->grid->id,
      ),
    );

    if( !empty($this->parameters) )
      $widgetOptions['parameters'] = $this->parameters;
    if( !empty($this->iframeAction) )
      $widgetOptions['iframeAction'] = $this->iframeAction;

    Yii::app()->controller->widget($this->widget, $widgetOptions);
  }
}