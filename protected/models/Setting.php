<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models
 *
 * @method static Setting model(string $class = __CLASS__)
 *
 * @property integer $id
 * @property string $param
 * @property string $value
 * @property string $notice
 */
class Setting extends FActiveRecord
{
  public function tableName()
  {
    return '{{settings}}';
  }
}