<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 */
class BAssignerButton extends BButton
{
  public $assignerOptions = array();

  public $bindHandlerByClass = null;

  public function run()
  {
    $id = Arr::get($this->htmlOptions, 'id', $this->getId());
    $this->htmlOptions['id'] = $id;

    if( $this->bindHandlerByClass )
      $this->htmlOptions['class'] = Arr::get($this->htmlOptions, 'class', '').' '.$this->bindHandlerByClass;

    parent::run();

    $this->registerAssignerButtonScript($id);
  }

  public function registerAssignerButtonScript($id)
  {
    $selectSelector = $this->bindHandlerByClass ? '.'.$this->bindHandlerByClass : '#'.$id;

    Yii::app()->clientScript->registerScript('AssignerButtonScript'.$selectSelector, "
      jQuery(document).on('click', '{$selectSelector}', function(e){
        e.preventDefault();
        assigner.apply(this, ".CJavaScript::encode($this->assignerOptions).");
      });
    ");
  }
}