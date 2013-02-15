<?php
/**
 * Столбец для отображения даты в grid view с фильтром, позволяющим выбирать интервал даты
 *
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.widgets.grid.BDatePickerColumn
 */
class BDatePickerColumn extends BDataColumn
{
  protected function renderFilterDivContent()
  {
    if( $this->filter !== false )
    {
      $htmlOptionsFrom = array();
      $nameFrom        = $this->name.'_from';
      CHtml::resolveNameID($this->grid->filter, $nameFrom, $htmlOptionsFrom);
      echo CHtml::activeLabelEx($this->grid->filter, $nameFrom);

      Yii::app()->controller->widget('DatePickerWidget', array(
        'value' => $this->grid->filter->{$this->name."_from"},
        'htmlOptions' => $htmlOptionsFrom,
      ));

      echo '</div><div class="filter-container">';

      $htmlOptionsTo = array();
      $nameTo        = $this->name.'_to';
      CHtml::resolveNameID($this->grid->filter, $nameTo, $htmlOptionsTo);
      echo CHtml::activeLabelEx($this->grid->filter, $nameTo);

      Yii::app()->controller->widget('DatePickerWidget', array(
        'value' => $this->grid->filter->{$this->name."_to"},
        'htmlOptions' => $htmlOptionsTo,
      ));

      Yii::app()->clientScript->registerScript('re-install-date-picker', "function reinstallDatePicker(id, data) {
        $('#".$htmlOptionsFrom['id'].", #".$htmlOptionsTo['id']."').datepicker(jQuery.extend({showMonthAfterYear:false}, jQuery.datepicker.regional['ru'], {'showAnim':'fold'}));
      }", CClientScript::POS_END);
    }
  }
}