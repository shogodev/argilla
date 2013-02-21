<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.order.models
 *
 * @method static OrderProductHistory model(string $class = __CLASS__)
 *
 * @property integer $order_product_id
 * @property integer $product_id
 * @property string $url
 * @property string $img
 * @property string $articul
 */
class BOrderProductHistory extends BActiveRecord
{
  public function tableName()
  {
    return '{{order_product_history}}';
  }
}