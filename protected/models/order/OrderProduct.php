<?php
/**
 * @author    Vladimir Utenkov <utenkov@shogo.ru>
 * @link      https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license   http://argilla.ru/LICENSE
 */
/**
 * Class OrderProduct
 *
 * @property int    $id
 * @property int    $order_id
 * @property string $name
 * @property float  $price
 * @property int    $count
 * @property float  $discount
 * @property float  $sum
 */
class OrderProduct extends FActiveRecord
{
  public function getDbConnection()
  {
    return Yii::app()->commonDB;
  }

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
      'items' => [self::HAS_MANY, 'OrderProductItem', 'order_product_id'],
    ];
  }

  public function afterFind()
  {
    $this->discount = floatval($this->discount);
  }
}