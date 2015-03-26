<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 */
abstract class SActiveRecordBehavior extends CActiveRecordBehavior
{
  public function attach($owner)
  {
    parent::attach($owner);

    $this->init();
  }

  public function init()
  {

  }
} 