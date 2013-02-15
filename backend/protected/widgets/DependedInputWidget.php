<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.widgets.DependedInputWidget
 */
class DependedInputWidget extends CWidget
{
  /**
   * @var CActiveRecord
   */
  public $model;

  public $attribute;

  public $url;

  public $htmlOptions = array();

  public $data = array();

  protected $modelId;

  protected $inputs;

  protected $postData;

  protected $updateSelector;

  public function run()
  {
    $this->modelId = get_class($this->model);
    $listData      = $this->data['listData'];

    $this->setUrl();
    $this->setInputs();
    $this->setPostData();
    $this->setUpdateSelector();

    echo CHtml::activeDropDownList($this->model,
                                   $this->attribute,
                                   array('' => 'Не задано') + $listData,
                                   array(
                                     'ajax' => array(
                                       'type'   => 'POST',
                                       'dataType' => 'json',
                                       'url'    => $this->url,
                                       'update' => true,
                                       'success' => $this->updateSelector,
                                       'data'   => $this->postData,
                                   )));
  }

  protected function setUrl()
  {
    $this->url = Yii::app()->controller->createUrl('updateAssignment', array('id' => $this->model->getPrimaryKey()));
  }

  protected function setUpdateSelector()
  {
    $expression = '';

    foreach(array_keys($this->inputs) as $input)
      $expression .= 'if(html.'.$input.') jQuery("#'.$this->modelId.'_'.$input.'").html(html.'.$input.');';

    $this->updateSelector = new CJavaScriptExpression('function(html){'.$expression.'}');
    $this->updateSelector = CJavaScript::encode($this->updateSelector);
  }

  protected function setInputs()
  {
    $inputs = $this->data['inputs'];

    foreach($inputs as $key => $input)
    {
      $depended = is_array($input) ? $key : $input;

      $this->inputs[$depended] = array(
        'type'     => Arr::get($input, 'type', 'dropdown'),
      );
    }
  }

  protected function setPostData()
  {
    $modelId        = $this->modelId;
    $this->postData = array();

    $this->postData[$modelId] = array(
      'attribute' => $this->attribute,
      'value'     => 'js:this.value',
      'inputs'    => $this->inputs,
    );
  }
}