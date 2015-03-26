<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package 
 */
class TestForm extends CFormModel
{
  public $name;

  public function rules()
  {
    return array(
      array('name', 'required')
    );
  }
} 