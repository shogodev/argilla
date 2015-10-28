<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.widgets.form
 *
 * Bootstrap horizontal form input widget.
 *
 * @since 0.9.8
 */

Yii::import('bootstrap.widgets.input.BootInput');

class BInputHorizontal extends BInput
{
  /**
   * Runs the widget.
   */
  public function run()
  {
    echo CHtml::openTag('tr', array('class' => $this->getContainerCssClass()));
    parent::run();
    echo '</tr>';
  }

  /**
   * Returns the label for this block.
   * @return string the label
   */
  protected function getLabel()
  {
    ob_start();
    echo CHtml::openTag('th', array('class' => $this->getContainerCssClass()));
    echo $this->popupHint(parent::getLabel());
    echo '</th>';

    return ob_get_clean();
  }

  /**
   * Renders a text.
   * @return string the rendered content
   */
  protected function text()
  {
    $attribute = $this->attribute;
    $value     = $this->model->$attribute;
    $format    = Arr::get($this->data, 'format', null);

    switch($format)
    {
      case 'date':
        $value = strtotime($value) !== false ? Yii::app()->format->formatDatetime(strtotime($value)) : '';
        break;
      case 'price':
        $value = PriceHelper::price($value, Arr::get($this->data, 'suffix', ''), '');
        break;
    }

    echo $this->getLabel();
    echo '<td>';
    echo CHtml::tag('div', $this->htmlOptions, $value).PHP_EOL;
    echo '</td>';
  }

  /**
   * Renders a checkbox.
   * @return string the rendered content
   */
  protected function checkBox()
  {
     $this->label = CHtml::activeLabelEx($this->model, $this->attribute, array('class' => 'checkbox'));
    echo $this->getLabel();
    echo '<td>';
    echo $this->form->checkBox($this->model, $this->attribute, $this->htmlOptions).PHP_EOL;
    echo $this->getError().$this->getHint();
    echo '</td>';
  }

  /**
   * Renders a list of checkboxes.
   * @return string the rendered content
   */
  protected function checkBoxList()
  {
    echo $this->getLabel();
    echo '<td>';
    echo CHtml::openTag('div', CMap::mergeArray(array('style' => 'float: left'), isset($this->htmlOptions['id']) ? array('id' => $this->htmlOptions['id']) : array()));
    echo $this->form->checkBoxList($this->model, $this->attribute, $this->data, $this->htmlOptions);
    echo CHtml::closeTag('div');
    echo $this->getError().$this->getHint();
    echo '</td>';
  }

  /**
   * Renders a list of inline checkboxes.
   * @return string the rendered content
   */
  protected function checkBoxListInline()
  {
    $this->htmlOptions['inline'] = true;
    $this->checkBoxList();
  }

  /**
   * Renders a drop down list (select).
   * @return string the rendered content
   */
  protected function dropDownList()
  {
    echo $this->getLabel();
    echo '<td>';
    echo $this->form->dropDownList($this->model, $this->attribute, $this->data, $this->htmlOptions);
    echo $this->getError().$this->getHint();
    echo '</td>';
  }

  /**
   * Renders a file field.
   * @return string the rendered content
   */
  protected function fileField()
  {
    echo $this->getLabel();
    echo '<td>';
    echo $this->form->fileField($this->model, $this->attribute, $this->htmlOptions);
    echo $this->getError().$this->getHint();
    echo '</td>';
  }

  /**
   * Renders a password field.
   * @return string the rendered content
   */
  protected function passwordField()
  {
    echo $this->getLabel();
    echo '<td>';
    echo $this->getPrepend();
    echo $this->form->passwordField($this->model, $this->attribute, $this->htmlOptions);
    echo $this->getAppend();
    echo $this->getError().$this->getHint();
    echo '</td>';
  }

  /**
   * Renders a radio button.
   * @return string the rendered content
   */
  protected function radioButton()
  {
    $attribute = $this->attribute;
    echo '<th>';
    echo '<label class="radio" for="'.$this->getAttributeId($attribute).'">';
    echo $this->model->getAttributeLabel($attribute);
    echo '</label>';
    echo '</th>';
    echo '<td>';
    echo $this->form->radioButton($this->model, $attribute, $this->htmlOptions).PHP_EOL;
    echo $this->getError().$this->getHint();
    echo '</td>';
  }

  /**
   * Renders a list of radio buttons.
   * @return string the rendered content
   */
  protected function radioButtonList()
  {
    echo $this->getLabel();
    echo '<td>';
    echo $this->form->radioButtonList($this->model, $this->attribute, $this->data, $this->htmlOptions);
    echo $this->getError().$this->getHint();
    echo '</td>';
  }

  /**
   * Renders a list of inline radio buttons.
   * @return string the rendered content
   */
  protected function radioButtonListInline()
  {
    $this->htmlOptions['inline'] = true;
    $this->radioButtonList();
  }

  /**
   * Renders a textarea.
   * @return string the rendered content
   */
  protected function textArea()
  {
    echo $this->getLabel();
    echo '<td>';
    echo $this->form->textArea($this->model, $this->attribute, $this->htmlOptions);
    echo $this->getError().$this->getHint();
    echo '</td>';
  }

  /**
   * Renders a text field.
   * @return string the rendered content
   */
  protected function textField()
  {
    echo $this->getLabel();
    echo '<td>';
    echo $this->getPrepend();
    echo $this->form->textField($this->model, $this->attribute, $this->htmlOptions);
    echo $this->getAppend();
    echo $this->getError().$this->getHint();
    echo '</td>';
  }

  /**
   * Renders a CAPTCHA.
   * @return string the rendered content
   */
  protected function captcha()
  {
    echo $this->getLabel();
    echo '<td><div class="captcha">';
    echo '<div class="widget">'.$this->widget('CCaptcha', $this->captchaOptions, true).'</div>';
    echo $this->form->textField($this->model, $this->attribute, $this->htmlOptions);
    echo $this->getError().$this->getHint();
    echo '</div></td>';
  }

  /**
   * Renders a CKEditor.
   * @return string the rendered content
   */
  protected function ckeditor()
  {
    echo $this->getLabel();
    echo '<td>';

    $this->widget('ext.wysiwyg.WysiwygWidget', array(
      'model' => $this->model,
      'attribute' => $this->attribute,
      'skin' => 'kama',
      'options' => array(
        'filebrowserBrowseUrl' => CHtml::normalizeUrl(array('/bFileUploader/elfinderCKEditor')),
        'filebrowserUploadUrl' => CHtml::normalizeUrl(array('/bFileUploader/quickUpload'))
      ),
    ));

    echo $this->getError().$this->getHint();
    echo '</td>';
  }

  protected function directory()
  {
    echo $this->getLabel();
    echo '<td>';
    $this->data->show();
    echo '</td>';
  }

  /**
   * Renders a DatePicker.
   * @return string the rendered content
   */
  protected function datePicker()
  {
    echo $this->getLabel();
    echo '<td>';
    echo $this->widget('DatePickerWidget', array('model' => $this->model, 'attribute' => $this->attribute), true);
    echo $this->getError().$this->getHint();
    echo '</td>';
  }

  protected function autocomplete()
  {
    $attribute = $this->attribute;

    echo $this->getLabel();
    echo '<td>';

    $this->widget('CAutoComplete',
      array(
          'model' => get_class($this->model),
          'name'  => get_class($this->model) . '[' . $attribute . ']',
          'value' => $this->model->$attribute,
          'url'   => Yii::app()->controller->createUrl('autocomplete', array(
            'model' => get_class($this->model),
            'field' => $attribute,
          )),
          'minChars' => 1,
      )
    );

    echo $this->getError().$this->getHint();
    echo '</td>';
  }

  /**
   * Renders an upload widget
   * @return string the rendered content
   */
  protected function upload()
  {
    echo '<th>';
    echo '<label>';
    echo $this->model->getAttributeLabel($this->attribute);
    echo '</label>';
    echo '</th>';
    echo '<td>';
    echo $this->widget('UploadWidget', array('model'       => $this->model,
                                             'attribute'   => $this->attribute,
                                             'multiple'    => $this->data['multiple'],
                                             'gridOptions' => isset($this->data['gridOptions']) ? $this->data['gridOptions'] : array(),
                                             'htmlOptions' => $this->htmlOptions,
                                            ), true);

    echo $this->getError().$this->getHint();
    echo '</td>';
  }

  protected function dependedInput()
  {
    echo $this->getLabel();
    echo '<td>';
    echo $this->widget('DependedInputWidget', array('model'       => $this->model,
                                                       'attribute'   => $this->attribute,
                                                       'htmlOptions' => $this->htmlOptions,
                                                       'data'        => $this->data,
                                                      ), true);

    echo $this->getError().$this->getHint();
    echo '</td>';
  }

  /**
   * Renders an uneditable field.
   * @return string the rendered content
   */
  protected function uneditableField()
  {
    echo $this->getLabel();
    echo '<td>';
    echo CHtml::tag('span', $this->htmlOptions, $this->model->{$this->attribute});
    echo $this->getError().$this->getHint();
    echo '</td>';
  }

  /**
   * @TODO допилить кастомные пути для файла
   *
   * @return void
   */
  protected function files()
  {
    $attribute = $this->attribute;

    echo $this->getLabel();
    echo '<td>';

    foreach( $this->model->$attribute as $file )
    {
      echo CHtml::link($file->name, '/f/' . lcfirst(get_class($this->model)) . '/' .  $file->name);
      echo '<br />';
    }

    echo '</td>';
  }

  /**
   * Renders related items.
   * @return string the rendered content
   */
  protected function related()
  {
    echo $this->widget('RelatedItemsWidget', array('model' => $this->model, 'relation' => $this->attribute, 'attributes' => $this->data), true);
    echo $this->getError().$this->getHint();
  }

  /**
   * Renders an coordinates field.
   * @return string the rendered content
   */
  protected function coordinates()
  {
    echo $this->getLabel();
    echo '<td>';
    echo $this->widget('CoordinateSetter', array('model' => $this->model, 'attribute' => $this->attribute), true);
    echo $this->getError().$this->getHint();
    echo '</td>';
  }

  protected function association()
  {
    if( isset($this->data['label']) )
      $this->label = Arr::cut($this->data, 'label', '');

    echo $this->getLabel();
    echo '<td>';
    echo $this->widget('BAssociationButton', $this->data, true);
    echo '</td>';
  }

  protected function content()
  {
    echo $this->getLabel();
    echo '<td>';
    echo $this->data;
    echo '</td>';
  }
}