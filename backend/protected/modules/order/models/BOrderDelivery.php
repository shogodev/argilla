<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 */

/**
 * Class BOrderDelivery
 *
 * @method static BOrderDelivery model(string $class = __CLASS__)
 *
 * @property string $order_id
 * @property string $delivery_type_id
 * @property string $delivery_price
 * @property string $address
 */
class BOrderDelivery extends BActiveRecord
{
  public function rules()
  {
    return array(
      array('order_id, delivery_type_id, delivery_price', 'length', 'max' => 10),
      array('address', 'length', 'max' => 255),
    );
  }

  public function attributeLabels()
  {
    return CMap::mergeArray(parent::attributeLabels(), array(
      'delivery_type_id' => 'Способ доставки',
      'delivery_price' => 'Стоимость доставки'
    ));
  }
}