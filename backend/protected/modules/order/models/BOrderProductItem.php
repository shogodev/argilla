<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.order.models
 *
 * @method static BOrderProductItem model(string $class = __CLASS__)
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
class BOrderProductItem extends BActiveRecord
{
  public function __get($name)
  {
    if( $name === 'name' )
      return parent::__get($name).': '.$this->getName();
    else
      return parent::__get($name);
  }

  public function rules()
  {
    return array(
      array('order_product_id, name, value', 'required'),
      array('amount', 'numerical', 'integerOnly' => true),
      array('order_product_id', 'length', 'max' => 10),
      array('type, name, value, pk, price', 'length', 'max' => 255),
      array('id, order_product_id, type, name, amount, value', 'safe', 'on' => 'search'),
    );
  }

  public function getName()
  {
    return $this->value;
  }

  public function getCount()
  {
    return $this->amount;
  }

  public function getSum()
  {
    return $this->amount * $this->price;
  }

  public function getDiscount()
  {
    return 0;
  }
}