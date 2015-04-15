<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.widgets
 */
Yii::import('zii.widgets.CListView');

class FListView extends CListView
{
  public $template = "{pager}\n{items}\n{pager}";

  public $pager = array('class' => 'FLinkPager');

  public $separator = ' ';

  public $ajaxUpdate = false;

  public $emptyText = 'Ни одного элемента не найдено';

  public $sorterTemplate;

  public $cssFile = false;

  public $afterAjaxUpdate = '$.fn.yiiListView.afterAjaxHandler';

  public $columnsCount;

  public $lastItemClass = 'last';

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
      $this->owner->renderPartial($this->sorterTemplate, $this->viewData);
      echo $this->sorterFooter;
    }
    else
      parent::renderSorter();
  }

  public function renderEmptyText()
  {
    if( $this->emptyText )
      echo CHtml::tag('div', array('class' => 'empty m20'), $this->emptyText);
  }

  public function run()
  {
    $this->registerClientScript();

    if( $this->tagName !== null )
      echo CHtml::openTag($this->tagName, $this->htmlOptions)."\n";

    $this->renderContent();
    $this->renderKeys();

    if( $this->tagName !== null )
      echo CHtml::closeTag($this->tagName);
  }

  public function renderItems()
  {
    if( $this->itemsTagName !== null )
      echo CHtml::openTag($this->itemsTagName, array('class' => $this->itemsCssClass))."\n";

    $data = $this->dataProvider->getData();
    if( ($n = count($data)) > 0 )
    {
      $owner    = $this->getOwner();
      $viewFile = $owner->getViewFile($this->itemView);
      $j        = 0;
      foreach($data as $i => $item)
      {
        $this->viewData['index'] = $i;
        $data = $this->viewData;
        $data['data'] = $item;
        $data['widget'] = $this;
        $owner->renderFile($viewFile, $data);
        if( $j++ < $n - 1 )
          echo $this->separator;
      }
    }
    else
      $this->renderEmptyText();

    if( $this->itemsTagName !== null )
      echo CHtml::closeTag($this->itemsTagName);
  }

  public function renderKeys()
  {
    echo '<!--noindex-->';
    parent::renderKeys();
    echo '<!--/noindex-->';
  }

  public function registerClientScript()
  {
    parent::registerClientScript();
    $cs = Yii::app()->clientScript;
    $cs->unregisterScriptFile($this->baseScriptUrl.'/jquery.yiilistview.js', CClientScript::POS_END);
  }

  /**
   * @return string
   */
  public function getColumnClass()
  {
    if( !empty($this->columnsCount) )
      return ($this->viewData['index'] + 1) % $this->columnsCount == 0 ? 'last' : '';
    else
      return '';
  }
}