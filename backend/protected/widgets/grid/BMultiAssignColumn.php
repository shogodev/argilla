<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.widgets.BMultiAssignColumn
 */
class BMultiAssignColumn extends BDataColumn
{
  protected function renderDataCellContent($row, $data)
  {
    $result    = '';
    $attribute = BProductAssignment::model()->toToAssignmentAttribute($this->name);
    $value     = $data->$attribute;

    if( $value )
    {
      $ids    = CHtml::listData($value, 'id', 'id');
      $models = Arr::reset($value)->findAllByPk(array_keys($ids));

      if( !empty($models) )
        $result = implode(", ", CHtml::listData($models, 'id', 'name'));
    }

    echo $result;
  }
}