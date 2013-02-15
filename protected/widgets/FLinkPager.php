<?php
/**
 * User: Sergey Glagolev <glagolev@shogo.ru>
 * Date: 26.09.12
 */

class FLinkPager extends CLinkPager
{
  public $cssFile = false;

  public $footer;

  public $previousPageCssClass = 'previous';

  public $nextPageCssClass = 'next';

  public $renderFirstAndLast = false;

  public function init()
  {
    if( !isset($this->htmlOptions['id']) )
      $this->htmlOptions['id'] = $this->getId();
    if( !isset($this->htmlOptions['class']) )
      $this->htmlOptions['class'] = 'pager';
  }

  public function run()
  {
    $this->registerClientScript();
    $buttons = $this->createPageButtons();

    if( !$this->renderFirstAndLast )
      $buttons = array_slice($buttons, 1, count($buttons) - 2);

    echo $this->header;

    if( !empty($buttons) )
      echo CHtml::tag('ul', $this->htmlOptions, implode("\n", $buttons));

    echo $this->footer;
  }
}