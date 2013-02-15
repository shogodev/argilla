<?php
/**
 * User: Sergey Glagolev <glagolev@shogo.ru>
 * Date: 26.09.12
 */

Yii::import('zii.widgets.CListView');

class FListView extends CListView
{
  public $template = "{pager}\n{items}\n{pager}";

  public $pager = array('class' => 'FLinkPager');

  public $ajaxUpdate = false;

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

    echo '<div class="'.$this->pagerCssClass.'">';
    $this->widget($class, $pager);
    echo '</div>';
  }

  public function renderEmptyText()
  {
    $emptyText = 'Ни одного элемента не найдено';
    echo CHtml::tag('div', array('class' => 'empty m20'), $emptyText);
  }
}