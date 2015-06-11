<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 */
abstract class ImageGrid extends BGridView
{
  public $attribute;

  /**
   * @var CActiveRecord|UploadBehavior
   */
  public $model;

  public $template = "{filters}\n{buttons}\n{summary}\n{dropZoneText}\n{items}\n{pagesize}\n{pager}\n{buttons}\n{scripts}";

  public function init()
  {
    if( $this->model->isNewRecord )
      $this->emptyText = 'Для загрузки изображений сохраните страницу';

    $this->dataProvider = $this->model->getUploadedFiles();

    parent::init();
  }

  protected function renderDropZoneText()
  {
    if( !$this->model->isNewRecord )
      echo '  Для загрузки изображений перетащите их в эту область.';
  }

  protected function imageColumn()
  {
    $this->columns[] = array(
      'header' => 'Изоб.',
      'class' => 'EImageColumn',
      'imagePathExpression' => '!empty($data["thmb"]) ? $data["thmb"] : $data["path"]',
      'htmlOptions' => array('class' => 'center image-column', 'style' => 'width:6.5%'),
      'style' => 'max-width: 20px; cursor: pointer',
    );
  }

  protected function buttonColumn()
  {
    $this->columns[] = array(
      'class' => 'BButtonColumn',
      'template' => '{delete}',
      'deleteButtonUrl' => function ($data) {
        return Yii::app()->controller->createUrl('upload', array(
          'id' => $this->model->id,
          'model' => get_class($this->model),
          'fileId' => $data['id'],
          'attr' => $this->attribute,
          'method' => 'delete'));
      },
    );
  }
}