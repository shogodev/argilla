<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.product.components
 */

/**
 * Class ProductImageGrid
 * @property BProduct $model
 */
class ProductImageGrid extends MultiImageGrid
{
  protected function gridColumns()
  {
    parent::gridColumns();

    if( $colors = $this->model->getParameterVariants('color') )
    {
      unset($this->columns[count($this->columns) - 1]);
      $this->columns[] = array(
        'name' => 'notice',
        'header' => 'Цвет',
        'class' => 'OnFlyEditField',
        'dropDown' => CMap::mergeArray(array('0' => 'Не задано'), CHtml::listData($colors, 'id', 'name')),
        'gridId' => $this->id,
        'htmlOptions' => array('class' => 'span2')
      );
    }
  }
}