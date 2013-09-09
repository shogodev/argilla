<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.widgets
 */
class FLinkPager extends CLinkPager
{
  public $cssFile = false;

  public $footer;

  public $previousPageCssClass = 'previous';

  public $nextPageCssClass = 'next';

  public $renderFirstAndLast = false;

  public $renderNextAndPrevious = true;

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

    if( !$this->renderNextAndPrevious )
      $buttons = array_slice($buttons, 1, count($buttons) - 2);

    echo $this->header;

    if( !empty($buttons) )
      echo CHtml::tag('ul', $this->htmlOptions, implode("\n", $buttons));

    echo $this->footer;
  }
}