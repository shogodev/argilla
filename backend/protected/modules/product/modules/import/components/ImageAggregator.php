<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 */

Yii::import('backend.modules.product.modules.import.components.abstracts.AbstractAggregator');

class ImageAggregator extends AbstractAggregator
{
  public $imagesColumns = array();

  public $groupByColumn;

  public $replace = array();

  public function init()
  {
    if( !is_numeric($this->groupByColumn) )
      $this->groupByColumn = ImportHelper::lettersToNumber($this->groupByColumn);

    $this->convertColumnIndexes($this->imagesColumns);
  }

  public function process($data, $rowIndex, $file)
  {
    foreach($this->imagesColumns as $column)
    {
      if( !empty($data[$column]) )
      {
        $this->data[$data[$this->groupByColumn]][$column] = strtr($data[$column], $this->replace);
      }
    }
  }
}