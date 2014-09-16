<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.product
 *
 * @method static BProductColorType model(string $class = __CLASS__)
 *
 * @property integer $id
 * @property string $name
 */
class BProductColorType extends BActiveRecord
{
  public function rules()
  {
    return array(
      array('name', 'required'),
    );
  }
}