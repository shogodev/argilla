<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.widgets.form
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

  public function run()
  {
    parent::run();

    Yii::app()->clientScript->registerScript(__CLASS__.'AjaxError#'.$this->id, '
      $(document).ajaxError(function(event, xhr){
        ajaxUpdateError(xhr);
      });'
    );
  }

  /**
   * Выводит производний контент в табличном виде
   * @param $model
   * @param $attribute
   * @param $content - html код
   *
   * @return string
   *
   * пример:
   *  echo $form->contentRow($model, 'user_id', CHtml::link($model->user->info, $this->createUrl('/user/frontendUser/update', array('id' => $model->user->id))));
   */
  public function contentRow($model, $attribute, $content)
  {
    return $this->inputRow('content', $model, $attribute, $content);
  }

  /**
   * Renders a Text row.
   *
   * example:
   *
   * $form->textRow($model, $attribute, $htmlOptions, $options = array('format' => 'price', 'suffix' => '  руб.'))
   *
   * @param CModel $model the data model
   * @param string $attribute the attribute
   * @param array $htmlOptions additional HTML attributes
   * @param array $options
   *
   * @return string the generated row
   */
  public function textRow($model, $attribute, $htmlOptions = array(), $options = array())
  {
    return $this->inputRow('text', $model, $attribute, $options, $htmlOptions);
  }

  /**
   * Renders a Date text row.
   *
   * @param CModel $model the data model
   * @param string $attribute the attribute
   * @param array $htmlOptions additional HTML attributes
   * @param array $options
   *
   * @return string the generated row
   */
  public function dateTextRow($model, $attribute, $htmlOptions = array(), $options = array('format' => 'date'))
  {
    return $this->inputRow('text', $model, $attribute, $options, $htmlOptions);
  }

  /**
   * Renders a text field input row.
   *
   * @param CModel $model the data model
   * @param string $attribute the attribute
   * @param array $htmlOptions additional HTML attributes
   *
   * @return string the generated row
   */
  public function textFieldRow($model, $attribute, $htmlOptions = array())
  {
    return $this->inputRow(BInput::TYPE_TEXT, $model, $attribute, null, $htmlOptions);
  }

  /**
   * Renders a CKEditor row.
   *
   * @param CModel $model the data model
   * @param string $attribute the attribute
   * @param array $htmlOptions additional HTML attributes
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
   * @param array $htmlOptions additional HTML attributes
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
   * @param array $htmlOptions additional HTML attributes
   *
   * @return string the generated row
   */
  public function urlRow($model, $attribute, $htmlOptions = array())
  {
    return $this->inputRow(BInput::TYPE_TEXT, $model, $attribute, null, CMap::mergeArray(array('rel' => 'extender', 'data-extender' => 'translit', 'data-source' => 'input[name*="name"]'), $htmlOptions));
  }

  public function dropDownListRow($model, $attribute, $data = array(), $htmlOptions = array())
  {
    if( !empty($data) )
      return parent::dropDownListRow($model, $attribute, $data, $htmlOptions);
    else
      return $this->dropDownListDefaultRow($model, $attribute, $data, $htmlOptions);
  }

  /**
   * Renders a drop-down list input row.
   *
   * @param CModel $model the data model
   * @param string $attribute the attribute
   * @param array $data the list data
   * @param array $htmlOptions additional HTML attributes
   *
   * @return string the generated row
   */
  public function dropDownListDefaultRow($model, $attribute, $data = array(), $htmlOptions = array())
  {
    return $this->inputRow(BInput::TYPE_DROPDOWN, $model, $attribute, array('' => 'Не задано') + $data, $htmlOptions);
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

  /**
   * Строит цепочку зависимых dropDown и checkBox листов (что именно выводить зависит от типа релейшена)
   *
   * @param BActiveRecord $model
   * @param $chain array
   * @param $htmlOptionsArray array
   *
   * @return string
   * @throws CHttpException
   *
   * Примеры
   * echo $form->dependedInputsChainRow($model, array('category_id', 'collection_id'));
   * echo $form->dependedInputsChainRow($model, array('category_id', 'collection_id', 'type_id', 'section_id'));
   */
  public function dependedInputsChainRow($model, $chain, $htmlOptionsArray = array())
  {
    if( !is_array($chain) )
      throw new CHttpException(500, 'Параметр $chain должен быть массивом');

    if( count($chain) < 2 )
      throw new CHttpException(500, 'Параметр $chain должен содержать минимум 2 связанных эламента');

    $html = array();
    $parentAttribute = null;

    foreach($chain as $index => $tmp)
    {
      if( count($chain) == 1 )
        break;

      $attribute = $chain[$index];
      unset($chain[$index]);

      $html[] = $this->inputRow('dependedInput', $model, $attribute, array(
        'listData' => $this->getListDataByAttribute($attribute, $parentAttribute, $model),
        'inputs' => array(Arr::reset($chain))
      ), Arr::get($htmlOptionsArray, $attribute, array()));

      $parentAttribute = $attribute;
    }

    $html[] = $this->getDependedRows($model, $attribute, array(Arr::reset($chain)));

    return implode('', $html);
  }

  /**
   * @param $model
   * @param $attribute
   * @param array $inputs
   * @param array $data
   * @param array $htmlOptions
   *
   * @return string
   * Examples:
   * two depended inputs [section] - [type]
   * echo $form->dependedInputs($model, 'section_id', array('type_id'), BProductType::model()->listData());
   * several depended inputs [section] - [type,category]
   * echo $form->dependedInputs($model, 'section_id', array('type_id', 'category_id'), BProductType::model()->listData());
   * three inputs depended each other [section] - [type] - [category]
   * echo $form->inputRow('dependedInput', $model, 'section_id', array('listData' => BProductSection::model()->listData(), 'inputs' => array('type_id')));
   * echo $form->dependedInputs($model, 'type_id', array('category_id'), BProductType::model()->listData());
   */
  public function dependedInputs($model, $attribute, array $inputs, $data = array(), $htmlOptions = array())
  {
    $data = array(
      'listData' => $data,
      'inputs' => $inputs,
    );

    $mainRow = $this->inputRow('dependedInput', $model, $attribute, $data, $htmlOptions);
    $dependedRows = $this->getDependedRows($model, $attribute, $inputs);

    return $mainRow.$dependedRows;
  }

  /**
   * Renders an Upload row
   *
   * @param $model
   * @param $attribute
   * @param bool $multiple
   * @param array $htmlOptions
   * @param array $gridOptions
   *
   * @return string
   * Example:
   * $form->uploadRow($model, 'product_img', true, array(), array('class' => 'ProductImageGrid'));
   * $form->uploadRow($model, 'product_img', true, array('id' => 'BProductImg'), array('class' => 'ProductImageGrid'));
   */
  public function uploadRow($model, $attribute, $multiple = true, $htmlOptions = array(), $gridOptions = array())
  {
    return $this->inputRow(
      'upload',
      $model,
      $attribute,
      CMap::mergeArray(
        array('multiple' => $multiple),
        array('gridOptions' => $gridOptions)
      ),
      $htmlOptions
    );
  }

  /**
   * Renders related items row
   *
   * @param $model
   * @param $relation
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
   * @param $model
   * @param $label
   * @param $destinationClassName
   * @param $iframeAction
   * @param array $parameters
   *
   * @return string
   */
  public function associationButtonRow($model, $label, $destinationClassName, $iframeAction, $parameters = array())
  {
    $parameters['model'] = $model;
    $parameters['label'] = $label;
    $parameters['name'] = $destinationClassName;
    $parameters['iframeAction'] = $iframeAction;

    return $this->inputRow('association', $model, null, $parameters);
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
   * @param BActiveRecord $model
   * @param string $attribute
   * @param array|string $inputs
   *
   * @return string
   */
  private function getDependedRows($model, $attribute, $inputs)
  {
    $dependedRows = '';

    $inputs = is_array($inputs) ? $inputs : array($inputs);

    foreach($inputs as $key => $input)
    {
      // опледеление по релашену
      $dependedAttribute = $input;
      $relationName = BProductStructure::getRelationName(BProductStructure::getModelName($dependedAttribute));
      $relation = $model->getActiveRelation($relationName);
      $type = $relation instanceof CHasManyRelation ? 'checkboxlist' : 'dropdown';

      switch($type)
      {
        case 'dropdown':
          $dependedRows .= $this->dropDownDependedRows($model, $attribute, $dependedAttribute);
          break;
        case 'checkboxlist':
          $dependedRows .= $this->checkBoxListDependedRows($model, $attribute, $dependedAttribute);
      }
    }

    return $dependedRows;
  }

  /**
   * @param $attribute
   * @param null $parentAttribute
   * @param null $model
   *
   * @throws CHttpException
   * @return array
   */
  private function getListDataByAttribute($attribute, $parentAttribute = null, $model = null)
  {
    $modelName = BProductStructure::getModelName($attribute);
    if( !class_exists($modelName) )
      throw new CHttpException(500, 'Не удалось найти модель '.$modelName);

    /**
     * @var BActiveRecord $productStructure
     */
    $productStructure = $modelName::model();

    $criteria = null;
    if( isset($parentAttribute, $model->{$parentAttribute}) )
    {
      $criteria = new CDbCriteria();
      $criteria->join = ' JOIN '.$productStructure->dbConnection->schema->quoteTableName(BProductTreeAssignment::model()->tableName()).' AS tree_assignment ON dst_id = :dst_id AND dst = :dst AND src = :src';
      $criteria->params = array(
        ':dst_id' => $model->{$parentAttribute},
        ':dst' => BProductStructure::getRelationName(BProductStructure::getModelName($parentAttribute)),
        ':src' => BProductStructure::getRelationName($modelName),
      );
      $criteria->condition = 'tree_assignment.src_id = t.id';
    }

    return $productStructure->listData('id', 'name', $criteria);
  }

  /**
   * Renders a depended drop-down list rows.
   *
   * @param BProduct $model
   * @param $attribute
   * @param $dependedAttribute
   *
   * @return string
   */
  protected function dropDownDependedRows($model, $attribute, $dependedAttribute)
  {
    $assignmentModel = Arr::reduce($model->assignment) ? Arr::reduce($model->assignment) : new BProductAssignment();
    if( $assignmentModel->isNewRecord )
      $assignmentModel->setAttributes(Yii::app()->request->getPost('BProduct', array()), false);
    $depends = $assignmentModel->getDepends($attribute, $dependedAttribute);
    $dependedRow = $this->dropDownListDefaultRow($model, $dependedAttribute, CHtml::listData($depends, 'id', 'name'), array('class' => 'depended'));

    return $dependedRow;
  }

  /**
   * Renders a depended check box list rows.
   *
   * @param BProduct $model
   * @param $attribute
   * @param $dependedAttribute
   *
   * @return string
   */
  protected function checkBoxListDependedRows($model, $attribute, $dependedAttribute)
  {
    $assignmentModel = Arr::reduce($model->assignment) ? Arr::reduce($model->assignment) : BProductAssignment::model();
    $depends = $assignmentModel->getDepends($attribute, $dependedAttribute);

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