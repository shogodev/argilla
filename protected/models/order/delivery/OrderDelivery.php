<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 *
 * @method static BOrderDelivery model(string $class = __CLASS__)
 *
 * @property string $order_id
 * @property string $delivery_type_id
 * @property string $delivery_price
 * @property string $address
 *
 * @property OrderDeliveryType $deliveryType
 * @property Order $order
 */
class OrderDelivery extends FActiveRecord
{
  public function rules()
  {
    return array(
      array('delivery_type_id', 'required'),
      array('address', 'ExRequiredValidator', 'dependedAttribute' => 'delivery_type_id', 'dependedValue' => OrderDeliveryType::SELF_DELIVERY, 'not' => true),
      array('delivery_type_id, address, delivery_price', 'safe')
    );
  }

  public function relations()
  {
    return array(
      'deliveryType' => array(self::BELONGS_TO, 'OrderDeliveryType', 'delivery_type_id'),
      'order' => array(self::BELONGS_TO, 'Order', 'order_id'),
    );
  }

  public function beforeSave()
  {
    if( !parent::beforeSave() )
      return false;

    $this->delivery_price = $this->deliveryType->calcDelivery($this->order->sum);

    return true;
  }

  public function attributeLabels()
  {
    return array(
      'delivery_type_id' => 'Способ доставки',
      'address' => 'Адрес',
    );
  }
}