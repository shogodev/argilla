<?php
/**
 * @author Vladimir Utenkov <utenkov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.order
 *
 * Class OrderProduct
 *
 * @property int    $id
 * @property int    $order_id
 * @property string $name
 * @property float  $price
 * @property int    $count
 * @property float  $discount
 * @property float  $sum
 *
 * @property OrderProductHistory $history
 * @property OrderProductItem[] $items
 */
class OrderProduct extends FActiveRecord
{
  public function rules()
  {
    return [
      ['order_id', 'required'],
      ['name, price, count, sum, discount', 'safe'],
    ];
  }

  public function relations()
  {
    return [
      'history' => [self::HAS_ONE, 'OrderProductHistory', 'order_product_id'],
      'items' => [self::HAS_MANY, 'OrderProductItem', 'order_product_id', 'order' => 'type, name, value'],
    ];
  }

  public function afterFind()
  {
    $this->discount = floatval($this->discount);
  }

  public function getItemsType($type)
  {
    $items = array();

    foreach($this->items as $item)
      if( $item->type == $type )
        $items[] = $item;

    return $items;
  }
}