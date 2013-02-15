<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.widgets.DatePickerWidget
 */
Yii::import('zii.widgets.jui.CJuiDatePicker');

class DatePickerWidget extends CJuiDatePicker
{
  public $language = 'ru';

  public $options = array('showAnim' => 'fold');
}