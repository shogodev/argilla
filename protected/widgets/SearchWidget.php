<?php
/**
 * User: Sergey Glagolev <glagolev@shogo.ru>
 * Date: 12.10.12
 */

class SearchWidget extends CWidget
{
  public $htmlOptions;

  public $url;

  public $query = '';

  public function init()
  {
    if( !isset($this->htmlOptions['id']) )
      $this->htmlOptions['id'] = 'search';

    $this->url   = Yii::app()->createUrl('index/search');
    $this->query = CHtml::encode($this->query);
  }

  public function run()
  {
    echo '<!--noindex-->';
    echo CHtml::tag('div', $this->htmlOptions, false, false);
    echo CHtml::tag('form', array('action' => $this->url, 'method' => 'get'), false, false);
    echo CHtml::tag('input', array('class' => 'inp', 'type' => 'search', 'title' => 'Я ищу...', 'name' => 'search', 'value' => $this->query));
    echo CHtml::tag('input', array('type' => 'image', 'src' => 'i/sp.gif', 'alt' => 'Поиск'));
    echo CHtml::closeTag('form');
    echo CHtml::closeTag('div');
    echo '<!--/noindex-->';
  }
}