<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.order
 *
 * @method static OrderDeliveryType model(string $className = __CLASS__)
 *
 * @property int    $id
 * @property string $name
 * @property int    $position
 * @property string $notice
 * @property string $price
 * @property bool   $visible
 */
class OrderDeliveryType extends FActiveRecord
{
  const SELF_DELIVERY = 1;

  const DELIVERY_MOSCOW = 2;

  const DELIVERY_MOSCOW_REGION = 3;

  const DELIVERY_REGION = 4;

  const FREE_DELIVERY_LIMIT = null;


  public function defaultScope()
  {
    $alias = $this->getTableAlias(false, false);

    return [
      'condition' => $alias.'.visible = :visible',
      'order' => $alias.'.position',
      'params' => [
        ':visible' => '1',
      ],
    ];
  }

  public function calcDelivery($orderSum)
  {
    if( is_null($this->id) )
      return null;

    if( $this->id == self::SELF_DELIVERY )
      return 0;
    else
    {
      if( $this->isFreeDelivery($orderSum) )
        return 0;
      else
        return floatval($this->price);
    }

    return null;
  }

  public static function isFreeDelivery($orderSum)
  {
    return !is_null(self::FREE_DELIVERY_LIMIT) && $orderSum > self::FREE_DELIVERY_LIMIT;
  }
}