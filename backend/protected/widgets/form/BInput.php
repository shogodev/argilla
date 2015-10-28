<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.widgets.form
 */

Yii::import('bootstrap.widgets.input.TbInput');
Yii::import('backend.modules.settings.models.BHint');

abstract class BInput extends TbInput
{
  const TYPE_TEXT = 'textfield';

  public $popupHintText;

  public function run()
  {
    switch ($this->type)
    {
      case 'text':
        $this->text();
        break;

      case 'ckeditor':
        $this->ckeditor();
        break;

      case 'datePicker':
        $this->datePicker();
        break;

      case 'files':
        $this->files();
        break;

      case 'directory':
        $this->directory();
        break;

      case 'dependedInput':
        $this->dependedInput();
        break;

      case 'autocomplete':
        $this->autocomplete();
        break;

      case 'related':
        $this->related();
        break;

      case 'coordinates':
        $this->coordinates();
        break;

      case 'upload':
        $behavior = $this->model->behaviors();
        if( empty($behavior['uploadBehavior']) )
          throw new CException($this->type.': Failed to run widget! Model must realise `uploadBehavior` behavior.');
        $this->upload();
        break;

      case 'association':
        $this->association();
        break;

      case 'content':
        $this->content();
        break;

      default:
        parent::run();
    }
  }

  protected function processHtmlOptions()
  {
    if( $this->hasModel() && !empty($this->attribute) && empty($this->htmlOptions['hint']) )
      $this->htmlOptions['hint'] = $this->model->getHint($this->attribute);

    if( $this->hasModel() && !empty($this->attribute) && empty($this->htmlOptions['popupHint']) )
      $this->popupHintText = $this->model->getPopupHint($this->attribute);

    parent::processHtmlOptions();

    if( !empty($this->htmlOptions['popupHint']) )
      $this->popupHintText = Arr::cut($this->htmlOptions, 'popupHint');
  }

  protected function getLabel()
  {
    if( isset($this->label) )
      $this->labelOptions['label'] = $this->label;

    if($this->label !== false && !in_array($this->type, array('checkbox', 'radio')) && $this->hasModel())
      return $this->form->labelEx($this->model, $this->attribute, $this->labelOptions);
    else if( $this->label !== null )
      return $this->label;
    else
      return '';
  }

  protected function popupHint($label)
  {
    if( !empty($this->popupHintText) && preg_match('/<label([^>]*)>(.*)<\/label>/', $label, $matches) )
      $label = '<label'.$matches[1].'>'.$this->createPopupHint($matches[2], $this->popupHintText).'</label>';

    return $label;
  }

  protected function createPopupHint($label, $popupHint)
  {
    return CHtml::tag('span',
      array(
        'rel' => 'tooltip',
        'title' => $popupHint
      ),
      '<i class="icon-comment"></i>&nbsp;'.$label
    );
  }
}