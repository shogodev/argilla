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

  public function run()
  {
    $id = Arr::get($this->htmlOptions, 'id', $this->getId());
    $this->htmlOptions['id'] = $id;

    parent::run();

    $this->registerAssignerButtonScript($id);
  }

  public function registerAssignerButtonScript($id)
  {
    Yii::app()->clientScript->registerScript('AssignerButtonScript#'.$id, "
      jQuery(document).on('click', '#{$id}', function(e){
        e.preventDefault();
        assigner.apply(this, ".CJavaScript::encode($this->assignerOptions).");
      });
    ");
  }
}