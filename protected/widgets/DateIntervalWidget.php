<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.widgets
 * Пример использования в формах
 * <div>
 * 'elements' => array(
 *   ...
 *   'birthday' => array(
 *     'type' => 'DateIntervalWidget',
 *     'form' => $this,
 *     'template' => '<span class="select-container form-size-third date-select">{day}</span>
 *                    <span class="select-container form-size-third date-select">{month}</span>
 *                    <span class="select-container form-size-third date-select">{year}</span>',
 *     'attribute' => 'birthday',
 *     'rangeYears' => array(intval(date("Y"))-100, intval(date("Y"))-5),
 *   ),
 *   ...
 * ),
 *
 * или
 *
 * 'elements' => array(
 *   ...
 *   'birthday' => array(
 *     'type' => 'DateIntervalWidget',
 *     'form' => $this,
 *     'layout' => '{input}'
 *     'template' => '<div class="form-row m20">{label}<div class="form-field">
 *                     <span class="select-container form-size-third date-select">{day}</span>
 *                     <span class="select-container form-size-third date-select">{month}</span>
 *                     <span class="select-container form-size-third date-select">{year}</span>
 *                    {error}</div></div>',
 *     'attribute' => 'birthday',
 *     'rangeYears' => array(intval(date("Y"))-100, intval(date("Y"))-5),
 *   ),
 *   ...
 * ),
 * </div>
 */
class DateIntervalWidget extends CWidget
{
  public $model;

  public $attribute;

  /**
   * @var FForm
   */
  public $form;

  public $rangeYears;

  /**
   * @var FFormInputElement
   */
  public $element;

  public $hideCalendar = true;

  public $template = '{day}{month}{year}{calendar}{input}{error}';

  /**
   * @var DateTime
   */
  private $selectData;

  public function init()
  {
    if( empty($this->rangeYears) )
      $this->rangeYears = array(intval(date("Y")), intval(date("Y")) + 1);

    if( isset($this->form->elements[$this->attribute]) )
      $this->element = $this->form->elements[$this->attribute];

    $this->selectData = DateTime::createFromFormat('d.m.Y', $this->model->{$this->attribute});
  }

  public function run()
  {
    echo strtr($this->template, array(
      '{label}' => $this->element->getLabel(),
      '{day}' => $this->getDays(),
      '{month}' => $this->renderMonths(),
      '{year}' => $this->renderYears(),
      '{calendar}' => $this->renderCalendar(),
      '{error}' => $this->errorInMainLayout() ? '' : $this->renderError(),
    ));
    echo $this->renderInput();

    $this->registerScript();
  }

  private function renderInput()
  {
    $defaultData = !empty($this->form->model->{$this->attribute}) ? $this->form->model->{$this->attribute} : null;

    return CHtml::hiddenField(CHtml::resolveName($this->model, $this->attribute), $defaultData);
  }

  private function renderError()
  {
    return $this->form->getActiveFormWidget()->error($this->form->model, $this->attribute);
  }

  private function renderCalendar()
  {
    $this->registerCalendarScript();

    return CHtml::tag('div', array('class' => 'calendar m5', 'style' => $this->hideCalendar ? 'display: none;' : ''), true);
  }

  private function getDays()
  {
    $selectedValue = $this->selectData ? intval($this->selectData->format('d')) : null;
    return CHtml::dropDownList('day', $selectedValue, $this->valToKeys(range(1, 31)), array('class' => $this->getElementCssClass()));
  }

  private function renderMonths()
  {
    $selectedValue = $this->selectData ? intval($this->selectData->format('m')) : null;
    return CHtml::dropDownList('month', $selectedValue, Yii::app()->locale->getMonthNames(), array('class' => $this->getElementCssClass()));
  }

  private function renderYears()
  {
    $selectedValue = $this->selectData ? $this->selectData->format('Y') : null;
    return CHtml::dropDownList('year', $selectedValue, $this->valToKeys(range($this->rangeYears[0], $this->rangeYears[1])), array('class' => $this->getElementCssClass()));
  }

  private function getElementCssClass()
  {
    return CHtml::activeId($this->model, $this->attribute).'_element';
  }

  private function getAttributeId()
  {
    return CHtml::getIdByName(CHtml::resolveName($this->model, $this->attribute));
  }

  private function valToKeys($array)
  {
    $newArray = array();

    foreach($array as $value)
      $newArray[$value] = $value;

    return $newArray;
  }

  private function errorInMainLayout()
  {
    return strpos($this->element->getLayout(), '{error}') !== false;
  }

  private function registerCalendarScript()
  {
    Yii::app()->clientScript->registerScript(__CLASS__.__METHOD__, "
    $('.calendar').datePicker({inline:true}).bind('dateSelected', function(e, selectedDate) {
        var selector = '.".$this->getElementCssClass()."';
        $(selector + '[name=day]').val(selectedDate.getDate()).trigger('change');
        $(selector + '[name=month]').val(selectedDate.getMonth() + 1).trigger('change');
        $(selector + '[name=year]').val(selectedDate.getFullYear()).trigger('change');

        $('#".$this->getAttributeId()."').val(selectedDate.asString('dd.mm.yyyy')).trigger('change');
      });
    ");
  }

  private function registerScript()
  {
    Yii::app()->clientScript->registerScript(__CLASS__.__METHOD__, "
      $('.".$this->getElementCssClass()."').on('change', function(e) {
        var selector = '.".$this->getElementCssClass()."';
        var date = new Date(
          $(selector + '[name=year]').val(),
          $(selector + '[name=month]').val() - 1,
          $(selector + '[name=day]').val()
        );

        $('#".$this->getAttributeId()."').val(date.asString('dd.mm.yyyy')).trigger('change');
      });
    ");
  }
}