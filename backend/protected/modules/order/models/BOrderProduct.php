<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.order.models
 *
 * @method static BOrderProduct model(string $class = __CLASS__)
 *
 * @property integer $id
 * @property integer $order_id
 * @property string $name
 * @property string $price
 * @property integer $count
 * @property string $discount
 * @property string $sum
 */
class BOrderProduct extends BActiveRecord
{
  public function relations()
  {
    return array('history' => array(self::HAS_ONE, 'OrderProductHistory', 'order_product_id'));
  }
}