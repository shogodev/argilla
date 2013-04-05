<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 *
 * Examples:
 *
 * $this->widget('VKWidget', array('method' => 'like'));
 *
 * $this->widget('VKWidget', array('method' => 'comments'));
 *
 * $this->widget('VKWidget', array('method' => 'share'));
 *
 */
class VKWidget extends CWidget
{
  public $apiId = 3520584;

  public $openApiScript = '//vk.com/js/api/openapi.js?86';

  public $shareScript = 'http://vk.com/js/api/share.js?11';

  public $method;

  public $parameters;

  protected $container;

  protected $script;

  public function init()
  {
    $js = "if( window.VK ){VK.init({apiId: ".$this->apiId.", onlyWidgets: true});}";
    Yii::app()->clientScript->registerScript(__CLASS__, $js, CClientScript::POS_END);
  }

  public function run()
  {
    if( !$this->method )
      throw new CHttpException(500, 'Не задан обязательный параметр method');

    $widgetMethod = 'widget'.ucfirst($this->method);

    if( !method_exists($this, $widgetMethod) )
      throw new CHttpException(500, 'Метод '.$this->method.' виджета '.__CLASS__.' не существует');

    $this->container = 'vk_'.$this->method;

    $this->{$widgetMethod}();
    $this->renderWidget();
  }

  protected function widgetLike()
  {
    Yii::app()->clientScript->registerScriptFile($this->openApiScript);

    if( !$this->parameters )
    {
      $this->parameters = array(
        'type' => 'mini',
      );
    }

    $this->script = "VK.Widgets.Like('".$this->container."', ".CJavaScript::encode($this->parameters).");";
  }

  protected function widgetComments()
  {
    Yii::app()->clientScript->registerScriptFile($this->openApiScript);

    if( !$this->parameters )
    {
      $this->parameters = array(
        'limit' => 10,
        'width' => 500,
        'attach' => 'photo,video',
      );
    }

    $this->script = "VK.Widgets.Comments('".$this->container."', ".CJavaScript::encode($this->parameters).");";
  }

  protected function widgetShare()
  {
    Yii::app()->clientScript->registerScriptFile($this->shareScript);

    if( !$this->parameters )
    {
      $this->parameters = array(
        'type' => 'button',
        'text' => 'Сохранить',
      );
    }

    echo "<script type='text/javascript'>document.write(VK.Share.button(false, ".CJavaScript::encode($this->parameters)."));</script>";
    $this->container = false;
  }

  protected function renderWidget()
  {
    if( $this->script )
      Yii::app()->clientScript->registerScript(__CLASS__.$this->method, 'if( window.VK ){'.$this->script.'}', CClientScript::POS_END);

    if( $this->container )
      echo CHtml::tag('div', array('id' => $this->container), null, true);
  }
}