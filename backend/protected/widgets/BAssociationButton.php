<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.widgets
 */
class BAssociationButton extends CWidget
{
  public $name;

  public $model;

  public $count = null;

  public $iframeAction;

  public $ajaxAction = 'association';

  public $parameters = array();

  public $assignerOptions = array();

  protected $iframeUrl;

  protected $ajaxUrl;

  public function init()
  {
    $pk         = $this->model->getPrimaryKey();
    $parameters = array('popup' => true, 'srcId' => $pk, 'src' => get_class($this->model), 'dst' => $this->name);

    foreach($this->parameters as $parameter => $value)
      $parameters[ucfirst($this->name)."[".$parameter."]"] = $value;

    $this->iframeUrl = Yii::app()->controller->createUrl($this->iframeAction, $parameters);
    $this->ajaxUrl   = Yii::app()->controller->createUrl($this->ajaxAction, $parameters);

    if( isset($this->model->associations) )
      foreach($this->model->associations as $association)
        if( $association->dst == $this->name )
          $this->count++;
  }

  public function run()
  {
    echo CHtml::tag('a', array(
      'class' => 'btn-assign'.($this->count !== null ? " active" : ""),
      'rel' => 'tooltip',
      'data-original-title' => 'Привязка',
      'data-iframeurl' => $this->iframeUrl,
      'data-ajaxurl' => $this->ajaxUrl,
      'href' => '#'.($this->count !== null ? $this->count : ''),
      'onClick' => 'assigner.ajaxHandler(this,'.CJavaScript::encode($this->assignerOptions).')',
    ), '<span>'.$this->count.'</span>');
  }
}