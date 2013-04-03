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
    $parameters = array(
      'popup' => true,
      'srcId' => $this->model->getPrimaryKey(),
      'src' => get_class($this->model),
      'dst' => $this->name
    );

    $this->count = $this->getAssociationsCount($parameters);

    foreach($this->parameters as $parameter => $value)
      $parameters[ucfirst($this->name)."[".$parameter."]"] = $value;

    $this->iframeUrl = Yii::app()->controller->createUrl($this->iframeAction, $parameters);
    $this->ajaxUrl   = Yii::app()->controller->createUrl($this->ajaxAction, $parameters);
  }

  public function run()
  {
    echo CHtml::tag('a', array(
      'class' => 'btn-assign'.($this->count ? " active" : ""),
      'rel' => 'tooltip',
      'data-original-title' => 'Привязка',
      'data-iframeurl' => $this->iframeUrl,
      'data-ajaxurl' => $this->ajaxUrl,
      'href' => '#'.($this->count ? $this->count : ''),
      'onClick' => 'assigner.ajaxHandler(this,'.CJavaScript::encode($this->assignerOptions).')',
    ), $this->count ?  '<span>'.$this->count.'</span>' : '');
  }

  protected function getAssociationsCount($parameters)
  {
    $criteria = new CDbCriteria();

    $criteria->addColumnCondition(array('src' => $parameters['src']));
    $criteria->addColumnCondition(array('src_id' => $parameters['srcId']));
    $criteria->addColumnCondition(array('dst' => $parameters['dst']));

    return BAssociation::model()->count($criteria);
  }
}