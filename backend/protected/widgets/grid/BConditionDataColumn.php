<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.widgets.grid
 *
 * Столбец, который рендерит для грида произвольный класс исходя из условия $condition
 * evaluateExpression($condition) должно вернуть индекс класса в массиве $columns
 *
 * array(
 *   'name' => 'value',
 *   'header' => 'Значение',
 *   'htmlOptions' => array('class' => 'span4'),
 *   'class' => 'BConditionDataColumn',
 *
 *   'columns' => array(
 *     array('class' => 'BDataColumn'),
 *     array('class' => 'OnFlyEditField', 'ajaxUrl' => $this->createUrl('product/onflyedit')),
 *   ),
 *
 *   'condition' => '!empty($data["active"]) ? 1 : 0;'
 * ),
 */
class BConditionDataColumn extends BDataColumn
{
  public $columns;

  public $condition;

  public function init()
  {
    foreach($this->columns as $i => $column)
    {
      foreach(array('name', 'header', 'htmlOptions') as $value)
      {
        if( !isset($column[$value]) )
          $column[$value] = $this->{$value};
      }

      $column = Yii::createComponent($column, $this->grid);
      $column->init();

      $this->columns[$i] = $column;
    }

    parent::init();
  }

  /**
   * @param int $row
   * @param mixed $data
   */
  protected function renderDataCellContent($row, $data)
  {
    $index = $this->evaluateExpression($this->condition, array('row' => $row, 'data' => $data));
    $this->columns[$index]->renderDataCellContent($row, $data);
  }
}