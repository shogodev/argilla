<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.widgets.BAssociationColumn
 */
class BAssociationColumn extends BDataColumn
{
  public $filter = false;

  public $iframeAction;

  public $ajaxAction = 'association';

  public $htmlOptions = array('class' => 'button-column');

  public $parameters = array();

  protected function renderDataCellContent($row, $data)
  {
    $pk         = $data->getPrimaryKey();
    $parameters = array('popup' => true, 'srcId' => $pk, 'src' => strtolower(get_class($data)), 'dst' => $this->name);

    foreach($this->parameters as $parameter => $value)
      $parameters[ucfirst($this->name)."[".$parameter."]"] = $value;

    $iframeUrl = Yii::app()->controller->createUrl($this->iframeAction, $parameters);
    $ajaxUrl   = Yii::app()->controller->createUrl($this->ajaxAction, $parameters);

    $count = 0;
    foreach($data->associations as $association)
      if( $association->dst == $this->name )
        $count++;

    echo CHtml::tag('a', array(
      'class' => 'btn-assign'.($count ? " active" : ""),
      'rel' => 'tooltip',
      'data-original-title' => 'Привязка',
      'data-iframeurl' => $iframeUrl,
      'data-ajaxurl' => $ajaxUrl,
      'href' => '#'.($count ? $count : ''),
      'onClick' => 'assigner.ajaxHandler(this)',
    ), '<span>'.$count.'</span>');
  }
}