<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.product
 */
class ParamButtons extends BButtonColumn
{
  public $template = '{addParam} {update} {delete}';

  public function init()
  {
    parent::init();

    $this->buttons['addParam'] = array(
      'label' => 'Добавить параметр в группу',
      'url' => 'Yii::app()->controller->createUrl("create", array("parent" => $data->primaryKey))',
      'icon' => 'pencil',
      'options' => array(
        'class' => 'add'
      )
    );
  }

  protected function renderButton($id, $button, $row, $data)
  {
    if( $id === 'addParam' && !$data->isGroup() )
      return;

    parent::renderButton($id, $button, $row, $data);
  }
}