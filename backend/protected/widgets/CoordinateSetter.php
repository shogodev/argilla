<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.widgets.CoordinateSetter
 */
class CoordinateSetter extends CWidget
{
  public $model;

  public $attribute;

  protected $className;

  public function init()
  {
    if( !isset($this->attribute) )
      throw new CHttpException(500, 'Не задано обязательное свойство attribute');

    if( !isset($this->model) )
      throw new CHttpException(500, 'Не задано обязательное свойство model');
  }

  public function run()
  {
    /**
     * @var BActiveForm|TbActiveForm $form
     */
    $form = $this->owner->form;
    $url  = Yii::app()->controller->createUrl('setCoordinates', array('popup' => true, 'id' => $this->model->id, 'attribute' => $this->attribute));

    echo CHtml::tag('div', array(
      'class' => 'coordinateSetter',
      'data-iframeurl' => $url,
      'onClick' => 'assigner.ajaxHandler(this, {width : "800", height : "500"}); return false',
    ), '&nbsp;');

    echo $form->textField($this->model, $this->attribute, array('class' => 'span4'));
  }
}