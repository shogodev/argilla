<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.product
 *
 * @method static ProductCollection model(string $class = __CLASS__)
 *
 * @property integer $id
 * @property integer $position
 * @property string  $name
 * @property string  $url
 * @property string  $img
 * @property string  $notice
 * @property integer $visible
 */
class ProductCollection extends FActiveRecord
{
  public function defaultScope()
  {
    $alias = $this->getTableAlias(false, false);

    return array(
      'condition' => $alias.'.visible=1',
      'order' => $alias.'.position',
    );
  }
}