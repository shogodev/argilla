<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.menu.components.grid
 */
class BButtonMenu extends BButtonColumn
{
  public $template = '{updateItem} {delete}';

  public function init()
  {
    $closeOperation = "function(){ $.fn.yiiGridView.update('{$this->grid->id}')}";

    $script = "function(e) {
      e.preventDefault();
      assigner.open(this.href, {'closeOperation' : {$closeOperation}});
    }";

    $this->buttons['updateItem'] = array(
      'label' => '',
      'url' => 'Yii::app()->controller->createUrl("menuCustomItem/update", array("id" => $data->primaryKey, "popup" => true))',
      'click' => new CJavaScriptExpression($script),
      'options' => array(
        'rel' => 'tooltip',
        'class' => 'update',
        'data-original-title' => 'Редактировать',
      )
    );

    parent::init();

    $this->buttons['delete']['url'] = 'Yii::app()->controller->createUrl("menuCustomItem/delete", array("id" => $data->primaryKey))';
  }

  protected function renderButton($id, $button, $row, $data)
  {
    if( !($data->model instanceof	BFrontendCustomMenuItem) )
      return;

    return parent::renderButton($id, $button, $row, $data);
  }
}
