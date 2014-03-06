<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.widgets.form
 */
Yii::import('bootstrap.widgets.TbButton');

class BButton extends TbButton
{
  /**
   * Зависимость отображения от режима popup
   *
   * @var bool
   */
  public $popupDepended = false;

  public function run()
  {
    if( $this->popupDepended === false || !Yii::app()->controller->popup )
      parent::run();
  }
}