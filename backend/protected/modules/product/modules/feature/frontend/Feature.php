<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 *
 * @method static Feature model(string $class = __CLASS__)
 *
 * @property integer $id
 * @property integer $position
 * @property string $image
 * @property string $name
 * @property string $notice
 */
class Feature extends FActiveRecord
{
  public function behaviors()
  {
    return array(
      'imageBehavior' => array('class' => 'SingleImageBehavior', 'path' => 'product'),
    );
  }

  public function defaultScope()
  {
    return array(
      'order' => "IF(position=0, 999999999, position), id",
    );
  }
}