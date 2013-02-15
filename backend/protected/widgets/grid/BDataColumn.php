<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.widgets.grid.BDataColumn
 */
Yii::import('bootstrap.widgets.TbDataColumn');

class BDataColumn extends TbDataColumn
{
  /**
   * @var bool $hideColumn дает возможность скрыть столбец, но оставить его в фильтре
   */
  public $hideColumn;

  public function renderFilterCell()
  {
    if( $this->grid->filterPosition !== BGridView::FILTER_POS_SEPARATE )
      parent::renderFilterCell();

    $this->renderFilterDivContent();
  }

  public function renderDataCell($row)
  {
    if( $this->hideColumn )
      return;

    parent::renderDataCell($row);
  }

  public function renderHeaderCell()
  {
    if( $this->hideColumn )
      return;

    parent::renderHeaderCell();
  }

  public function renderFooterCell()
  {
    if( $this->hideColumn )
      return;

    parent::renderFooterCell();
  }

  protected function renderFilterDivContent()
  {
    if( is_string($this->filter) )
      echo $this->filter;
    else if( $this->filter !== false && $this->grid->filter !== null && $this->name !== null && strpos($this->name, '.') === false )
    {
      echo CHtml::activeLabel($this->grid->filter, $this->name, array('id' => false));

      if( is_array($this->filter) )
        echo CHtml::activeDropDownList($this->grid->filter, $this->name, $this->filter, array('id' => false, 'prompt' => ''));
      else if( $this->filter === null )
        echo CHtml::activeTextField($this->grid->filter, $this->name, array('id' => false));
    }
  }
}