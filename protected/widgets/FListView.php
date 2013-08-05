<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.widgets
 */
Yii::import('zii.widgets.CListView');

class FListView extends CListView
{
  public $template = "{pager}\n{items}\n{pager}";

  public $pager = array('class' => 'FLinkPager');

  public $ajaxUpdate = false;

  public $emptyText = 'Ни одного элемента не найдено';

  public $sorterTemplate;

  public function renderPager()
  {
    if( !$this->enablePagination )
      return;

    if( is_array($this->pager) )
    {
      $pager = $this->pager;
      if( isset($pager['class']) )
        $class = Arr::cut($pager, 'class');
    }

    $pager['pages'] = $this->dataProvider->getPagination();

    if( $pager['pages']->pageCount <= 1 )
      return;

    echo '<div class="'.$this->pagerCssClass.'">';
    $this->widget($class, $pager);
    echo '</div>';
  }

  public function renderSorter()
  {
    if( $this->sorterTemplate )
    {
      echo $this->sorterHeader;
      $this->owner->renderPartial($this->sorterTemplate);
      echo $this->sorterFooter;
    }
    else
      parent::renderSorter();
  }

  public function renderEmptyText()
  {
    echo CHtml::tag('div', array('class' => 'empty m20'), $this->emptyText);
  }
}