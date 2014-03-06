<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models
 *
 * @method static Favorite model(string $class = __CLASS__)
 *
 * @property integer $id
 * @property string $type
 * @property integer $user_id
 * @property string $value
 */
class Favorite extends FActiveRecord
{
  public function rules()
  {
    return array(
      array('type, user_id, value', 'required')
    );
  }
}