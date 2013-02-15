<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.widgets.form.BActiveForm
 */
Yii::import('bootstrap.widgets.TbActiveForm');

class BActiveForm extends TbActiveForm
{
  const INPUT_HORIZONTAL = 'BInputHorizontal';

  public $type = 'horizontal';

  public $enableAjaxValidation = true;

  public function init()
  {
    if( !isset($this->htmlOptions['enctype']) )
      $this->htmlOptions['enctype'] = 'multipart/form-data';

    parent::init();
  }

  /**
   * Renders require form caption
   */
  public function renderRequire()
  {
    ob_start();
    echo CHtml::tag('p', array('class' => 'alert alert-info'));
    echo 'Поля, отмеченные знаком ';
    echo CHtml::tag('span', array('class' => 'required')).'*'.CHtml::closeTag('span');
    echo ', обязательны к заполнению.';
    echo CHtml::closeTag('p');

    return ob_get_clean();
  }

  /**
   * Renders a Text row.
   *
   * @param CModel $model the data model
   * @param string $attribute the attribute
   * @param array  $htmlOptions additional HTML attributes
   *
   * @return string the generated row
   */
  public function textRow($model, $attribute, $htmlOptions = array())
  {
    return $this->inputRow('text', $model, $attribute, null, $htmlOptions);
  }

  /**
   * Renders a Date text row.
   *
   * @param CModel $model the data model
   * @param string $attribute the attribute
   * @param array  $htmlOptions additional HTML attributes
   *
   * @return string the generated row
   */
  public function dateTextRow($model, $attribute, $htmlOptions = array())
  {
    return $this->inputRow('text', $model, $attribute, array('format' => 'date'), $htmlOptions);
  }

  /**
   * Renders a CKEditor row.
   *
   * @param CModel $model the data model
   * @param string $attribute the attribute
   * @param array  $htmlOptions additional HTML attributes
   *
   * @return string the generated row
   */
  public function ckeditorRow($model, $attribute, $htmlOptions = array())
  {
    return $this->inputRow('ckeditor', $model, $attribute, null, $htmlOptions);
  }

  /**
   * Renders a DatePicker row.
   *
   * @param CModel $model the data model
   * @param string $attribute the attribute
   * @param array  $htmlOptions additional HTML attributes
   *
   * @return string the generated row
   */
  public function datePickerRow($model, $attribute, $htmlOptions = array())
  {
    return $this->inputRow('datePicker', $model, $attribute, null, $htmlOptions);
  }

  public function directoryRow($model, $attribute, $directory, $htmlOptions = array())
  {
    return $this->inputRow('directory', $model, $attribute, $directory, $htmlOptions);
  }

  /**
   * Renders a Url row.
   *
   * @param CModel $model the data model
   * @param string $attribute the attribute
   * @param array  $htmlOptions additional HTML attributes
   *
   * @return string the generated row
   */
  public function urlRow($model, $attribute, $htmlOptions = array())
  {
    return $this->inputRow(TbInput::TYPE_TEXT, $model, $attribute, null, CMap::mergeArray(array('rel' => 'extender', 'data-extender' => 'translit', 'data-source' => 'input[name*="name"]'), $htmlOptions));
  }

  /**
   * Renders a drop-down list input row.
   * @param CModel $model the data model
   * @param string $attribute the attribute
   * @param array $data the list data
   * @param array $htmlOptions additional HTML attributes
   * @return string the generated row
   */
  public function dropDownListDefaultRow($model, $attribute, $data = array(), $htmlOptions = array())
  {
    return $this->inputRow(TbInput::TYPE_DROPDOWN, $model, $attribute, array('' => 'Не задано') + $data, $htmlOptions);
  }

  public function autocompleteRow($model, $attribute, $htmlOptions = array())
  {
    return $this->inputRow('autocomplete', $model, $attribute, null, $htmlOptions);
  }

  public function fileRow($model, $attribute, $htmlOptions = array())
  {
    return $this->inputRow('files', $model, $attribute, null, $htmlOptions);
  }

  public function coordinatesRow($model, $attribute, $htmlOptions = array())
  {
    return $this->inputRow('coordinates', $model, $attribute, null, $htmlOptions);
  }

  public function dependedInputs($model, $attribute, array $inputs, $data = array(), $htmlOptions = array())
  {
    $data = array(
      'listData' => $data,
      'inputs' => $inputs,
    );

    $mainRow = $this->inputRow('dependedInput', $model, $attribute, $data, $htmlOptions);
    $dependedRows = '';

    foreach($inputs as $key => $input)
    {
      $dependedAttribute = is_array($input) ? $key : $input;
      $type              = Arr::get($input, 'type', 'dropdown');

      switch($type)
      {
        case 'dropdown':
          $dependedRows .= $this->dropDownDependedRows($model, $attribute, $dependedAttribute);
          break;
        case 'checkboxlist':
          $dependedRows .= $this->checkBoxListDependedRows($model, $attribute, $dependedAttribute);
      }
    }

    return $mainRow.$dependedRows;
  }

  /**
   * Renders a assigned drop-down list rows.
   *
   * @param BProductTreeAssignment $model
   * @param array $data
   * @param array $htmlOptions
   *
   * @return string
   */
  public function dropDownAssignedRow($model, $data = array(), $htmlOptions = array())
  {
    return $this->dropDownListDefaultRow($model, $model::DST_FIELD, CHtml::listData($model->getValues(), 'id', 'name') + $data, $htmlOptions);
  }

  /**
   * Renders an Upload row
   *
   * @param       $model
   * @param       $attribute
   * @param bool  $multiple
   * @param array $htmlOptions
   *
   * @return string
   */
  public function uploadRow($model, $attribute, $multiple = true, $htmlOptions = array())
  {
    return $this->inputRow('upload', $model, $attribute, array('multiple' => $multiple), $htmlOptions);
  }

  /**
   * Renders related items row
   *
   * @param       $model
   * @param       $relation
   * @param array $attributes
   * @param array $htmlOptions
   *
   * @return string
   */
  public function relatedItemsRow($model, $relation, $attributes, $htmlOptions = array())
  {
    return $this->inputRow('related', $model, $relation, $attributes, $htmlOptions);
  }

  /**
   * Renders a depended drop-down list rows.
   *
   * @param BProduct $model
   * @param       $attribute
   * @param       $dependedAttribute
   *
   * @return string
   */
  protected function dropDownDependedRows($model, $attribute, $dependedAttribute)
  {
    $assignmentModel = Arr::reduce($model->assignment) ? Arr::reduce($model->assignment) : BProductAssignment::model();
    $depends         = $assignmentModel->getDepends($attribute, $dependedAttribute);
    $dependedRow     = $this->dropDownListDefaultRow($model, $dependedAttribute, CHtml::listData($depends, 'id', 'name'), array('class' => 'depended'));

    return $dependedRow;
  }

  /**
   * Renders a depended check box list rows.
   *
   * @param BProduct $model
   * @param       $attribute
   * @param       $dependedAttribute
   *
   * @return string
   */
  protected function checkBoxListDependedRows($model, $attribute, $dependedAttribute)
  {
    $assignmentModel = Arr::reduce($model->assignment) ? Arr::reduce($model->assignment) : BProductAssignment::model();
    $depends         = $assignmentModel->getDepends($attribute, $dependedAttribute);

    $dependedRow = $this->checkBoxListRow(
      $model,
      $dependedAttribute,
      CHtml::listData($depends, 'id', 'name'),
      array(
        'id' => get_class($model).'_'.$dependedAttribute,
        'uncheckValue' => null,
        'class' => 'depended',
      ));

    return $dependedRow;
  }

  /**
   * Returns the input widget class name suitable for the form.
   * @return string the class name
   */
  protected function getInputClassName()
  {
    if( isset($this->input) )
      return $this->input;
    else
    {
      return $this->type === static::TYPE_HORIZONTAL ? static::INPUT_HORIZONTAL : parent::getInputClassName();
    }
  }
}