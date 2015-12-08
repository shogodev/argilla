<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.order
 */

/**
 * Class OrderDeliveryType
 *
 * @method static OrderDeliveryType model(string $className = __CLASS__)
 *
 * @property int $id
 * @property string $name
 * @property int $position
 * @property string $notice
 * @property string $minimal_price
 * @property string $price
 * @property string $free_delivery_price_limit
 * @property bool $always_free_delivery
 * @property bool $visible
 */
class OrderDeliveryType extends FActiveRecord
{
  const SELF_DELIVERY = 1;

  const DELIVERY_MOSCOW = 2;

  const DELIVERY_MOSCOW_REGION = 3;

  const DELIVERY_REGION = 4;

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
      return 0;

    if( !$this->isFreeDelivery($orderSum) )
      return floatval($this->price);

    return 0;
  }

  public function isFreeDelivery($orderSum)
  {
    if( $this->always_free_delivery )
      return true;

    if( PriceHelper::isEmpty($this->free_delivery_price_limit) )
      return false;

    if( floatval($this->free_delivery_price_limit) <= $orderSum )
      return true;

    return false;
  }

  /**
   * @return string
   */
  public static function getJsonDeliveryData()
  {
    $orderPrice = Yii::app()->controller->basket->getSum();

    return CJSON::encode(CHtml::listData(self::model()->findAll(), 'id', function(OrderDeliveryType $deliveryType) use($orderPrice) {
      return array(
        'price' => floatval($deliveryType->price),
        'minimalPrice' => floatval($deliveryType->minimal_price),
        'freeDeliveryPrice' => $deliveryType->isFreeDelivery($orderPrice) ? 'free' : floatval($deliveryType->free_delivery_price_limit),
      );
    }));
  }
}