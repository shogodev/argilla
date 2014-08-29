<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package extensions.wysiwyg
 */
class WysiwygWidget extends CInputWidget
{
  public $language = 'ru';

  public $options = array();

  public $skin = 'kama';

  private $basePath;

  private $assetsPath;

  public function init()
  {
    if( !isset($this->model) )
    {
      throw new CHttpException(500, '"model" have to be set!');
    }

    if( !isset($this->attribute) )
    {
      throw new CHttpException(500, '"attribute" have to be set!');
    }

    $this->basePath = dirname(__FILE__);
    $this->assetsPath = Yii::app()->getAssetManager()->publish($this->basePath.DIRECTORY_SEPARATOR.'assets');

    $this->registerScripts();
  }

  public function run()
  {
    $this->htmlOptions['id'] = $this->id;

     echo CHtml::activeTextArea($this->model, $this->attribute, $this->htmlOptions);
  }

  private function getOptions()
  {
    return CMap::mergeArray($this->options, array(
      'language' => $this->language,
      'skin' => $this->skin
    ));
  }

  private function registerScripts()
  {
    Yii::app()->clientScript->registerScriptFile($this->assetsPath.'/ckeditor.js');

    $jsonOptions = CJSON::encode($this->getOptions());

    $script = "CKEDITOR.replace('{$this->id}', {$jsonOptions});";

    Yii::app()->clientScript->registerScript('WysiwygWidgetScript#'.$this->id, $script, CClientScript::POS_LOAD);
  }
}