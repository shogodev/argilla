<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 */
class ImageGrid
{
  public $widget;

  protected $gridId;

  /**
   * @var CModel|BActiveRecord
   */
  protected $model;

  protected $columns = array();

  public function getColumns()
  {
    return $this->columns;
  }

  public function __construct(UploadWidget $widget)
  {
    $this->widget = $widget;
    $this->gridId = $widget->htmlOptions['gridId'];
    $this->model  = $widget->model;

    $this->initColumns();
  }

  protected function initColumns()
  {
    $this->imageColumn();
    $this->gridColumns();
    $this->buttonColumn();
  }

  protected function imageColumn()
  {
    $this->columns[] = array(
      'header'              => 'Изоб.',
      'class'               => 'EImageColumn',
      'imagePathExpression' => '!empty($data["thmb"]) ? $data["thmb"] : $data["path"]',
      'htmlOptions'         => array('class' => 'center', 'style' => 'width:6.5%'),
      'style'               => 'max-width: 20px;',
    );
  }

  protected function gridColumns()
  {
    $this->columns[] = array('name' => 'position', 'header' => 'Позиция', 'class' => 'OnFlyEditField', 'gridId' => $this->gridId, 'htmlOptions' => array('class' => 'span2'));
    $this->columns[] = array('name' => 'type', 'header' => 'Тип', 'class' => 'OnFlyEditField', 'dropDown' => $this->model->imageTypes, 'gridId' => $this->gridId, 'htmlOptions' => array('class' => 'span2'));
    $this->columns[] = array('name' => 'size', 'header' => 'Размер', 'htmlOptions' => array('class' => 'span2'));
    $this->columns[] = array('name' => 'notice', 'class' => 'OnFlyEditField', 'gridId' => $this->gridId, 'header' => 'Описание', 'htmlOptions' => array('class' => ''));
  }

  protected function buttonColumn()
  {
    $this->columns[] = array(
      'class'           => 'bootstrap.widgets.TbButtonColumn',
      'template'        => '{delete}',
      'deleteButtonUrl' => function ($data){
        return Yii::app()->controller->createUrl('upload', array(
          'id'     => $this->model->id,
          'model'  => get_class($this->model),
          'fileId' => $data['id'],
          'attr'   => $this->widget->attribute,
          'method' => 'delete'));
      },
    );
  }
}