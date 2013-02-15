<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package extensions.wysiwyg
 */
class WysiwygWidget extends CInputWidget
{
  public $editor;

  public $basePath;

  public $defaultValue;

  public $config = array();

  public $class = 'CKeditor';

  public function run()
  {
    if( !isset($this->model) )
      throw new CHttpException(500, '"model" have to be set!');

    if( !isset($this->attribute) )
      throw new CHttpException(500, '"attribute" have to be set!');

    if( !isset($this->editor) )
      $this->editor = Yii::app()->getFrontendPath()."ckeditor/ckeditor.php";

    if( !isset($this->basePath) )
      $this->basePath = Yii::app()->getFrontendUrl()."ckeditor/";

    if( !isset($this->defaultValue) )
      $this->defaultValue = $this->model->{$this->attribute};

    require_once $this->editor;
    $this->renderEditor();
  }

  protected function renderEditor()
  {
    $editor           = new $this->class(get_class($this->model).'['.$this->attribute.']');
    $editor->basePath = $this->basePath;

    foreach($this->config as $key => $value)
      $editor->config[$key] = $value;

    $editor->editor(get_class($this->model).'['.$this->attribute.']', $this->defaultValue);
  }
}