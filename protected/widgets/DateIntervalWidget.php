<?php
class DateIntervalWidget extends CWidget
{
  public $model;

  public $attribute;

  public $form;

  public $rangeYears;

  public $value;

  public $hideCalendar = false;

  public function init()
  {
    if( empty($this->rangeYears) )
      $this->rangeYears = array(intval(date("Y")), intval(date("Y"))+1);

    if( empty($this->value) && isset($this->form->elements[$this->attribute]) )
      $this->value = $this->form->elements[$this->attribute];
  }

  public function run()
  {
    $view = $this->getViewFile('frontend.views.widgets.date_interval_widget');
    $this->renderFile($view);
  }

  protected function getDays()
  {
    return range(1, 31);
  }

  protected function getMonths()
  {
    return Yii::app()->locale->getMonthNames();
  }

  protected function getYears()
  {
    return range($this->rangeYears[0], $this->rangeYears[1]);
  }
}