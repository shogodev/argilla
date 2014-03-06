<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.widgets.grid
 */
class BImageColumn extends BDataColumn
{
  public $filter = false;

  protected function renderDataCellContent($row, $data)
  {
    if( !isset($this->htmlOptions['class']) )
      $options['class'] = 'small_img';

    if( !isset($this->htmlOptions['path']) )
      $options['path'] = Yii::app()->controller->module->getUploadUrl();
    else
      $options['path'] = Arr::cut($this->htmlOptions, 'path');

    if( !empty($data->{$this->name}) )
      echo CHtml::tag('img', CMap::mergeArray(array('src' => $options['path'].$data->{$this->name}, 'class' => $options['class']), $this->htmlOptions));
  }
}