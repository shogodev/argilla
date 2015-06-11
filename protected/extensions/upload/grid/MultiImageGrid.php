<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 */
class MultiImageGrid extends ImageGrid
{
  protected function initColumns()
  {
    $this->imageColumn();
    $this->gridColumns();
    $this->buttonColumn();

    parent::initColumns();
  }

  protected function gridColumns()
  {
    $this->columns[] = array('name' => 'position', 'header' => 'Позиция', 'class' => 'OnFlyEditField', 'gridId' => $this->id, 'htmlOptions' => array('class' => 'span2'));
    $this->columns[] = array('name' => 'type', 'header' => 'Тип', 'class' => 'OnFlyEditField', 'dropDown' => $this->model->imageTypes, 'gridId' => $this->id, 'htmlOptions' => array('class' => 'span2'));
    $this->columns[] = array('name' => 'size', 'header' => 'Размер', 'htmlOptions' => array('class' => 'span2'));
    $this->columns[] = array('name' => 'notice', 'class' => 'OnFlyEditField', 'gridId' => $this->id, 'header' => 'Описание', 'htmlOptions' => array('class' => ''));
  }
}