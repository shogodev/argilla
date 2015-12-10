<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.widgets.grid
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
    else
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
      echo CHtml::activeLabel($this->grid->filter, $this->name, array('id' => false, 'label' => $this->header));

      if( is_array($this->filter) )
      {
        $htmlOptions = CMap::mergeArray(
          array(
            'id' => false,
            'prompt' => ''
          ),
          $this->htmlOptions
        );

        echo CHtml::activeDropDownList($this->grid->filter, $this->name, $this->filter, $htmlOptions);
      }
      else if( $this->filter === null )
      {
        $htmlOptions = CMap::mergeArray(
          array(
            'id' => false,
          ),
          $this->htmlOptions
        );

        echo CHtml::activeTextField($this->grid->filter, $this->name, $htmlOptions);
      }
    }
  }
}