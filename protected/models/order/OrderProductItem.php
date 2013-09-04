<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.order
 *
 * @method static OrderProductItem model(string $class = __CLASS__)
 *
 * @property integer $id
 * @property string $order_product_id
 * @property string $type
 * @property string $pk
 * @property string $name
 * @property integer $amount
 * @property string $price
 * @property string $value
 */
class OrderProductItem extends FActiveRecord
{
  public function rules()
  {
    return array(
      array('order_product_id, name, value', 'required'),
      array('amount', 'numerical', 'integerOnly' => true),
      array('order_product_id', 'length', 'max'=>10),
      array('type, name, value, pk, price', 'length', 'max'=>255),
      array('id, order_product_id, type, name, amount, value', 'safe', 'on' => 'search'),
    );
  }

  public function __toString()
  {
    return mb_strtolower($this->name.': '.$this->value);
  }
}