<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
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
 * @property string $fullName
 *
 * @property string $itemsData
 * @property BOrderProductHistory $history
 * @property BOrder $order
 * @property boolean $allowedAddParameter
 */
class BOrderProduct extends BActiveRecord
{
  const ORDER_PARAMETER_KEY = 'basket';

  public function rules()
  {
    return array(
      array('order_id', 'required'),
      array('name, price, count, sum, discount', 'safe'),
    );
  }

  public function relations()
  {
    return array(
      'history' => array(self::HAS_ONE, 'BOrderProductHistory', 'order_product_id'),
      'order' => array(self::BELONGS_TO, 'BOrder', 'order_id'),
      'items' => array(self::HAS_MANY, 'BOrderProductItem', 'order_product_id', 'order' => 'type, name, value'),
    );
  }

  /**
   * @param $type
   * @param array $typeExceptions
   *
   * @return OrderProductItem[]|array
   */
  public function getItems($type = null, array $typeExceptions = array())
  {
    $items = array();

    foreach($this->items as $item)
    {
      if( !is_null($type) && $item->type != $type )
        continue;

      if( in_array($item->type, $typeExceptions) )
        continue;

      $items[] = $item;
    }

    return $items;
  }

  /**
   * @param $type
   * @param $pk
   *
   * @return OrderProductItem|null
   */
  public function getItem($type, $pk = null)
  {
    foreach($this->items as $item)
    {
      if( $item->type == $type && (is_null($pk) ? 1 : $item->primaryKey == $pk) )
      {
        return $item;
      }
    }
    return null;
  }

  public function getFullName()
  {
    $name = array($this->name);
    if( !empty($this->history->articul) )
      array_push($name, "(Артикул: {$this->history->articul})");

    return implode(' ', $name);
  }

  public function recalc($save = true)
  {
    $this->refresh();

    $this->sum = $this->count * ($this->price - $this->discount);

    if( $save )
    {
      $this->save();
      $this->order->recalc();
    }
  }

  /**
   * @return int
   */
  public function getAllowedAddParameter()
  {
    return $this->getOrderParametersDataProvider()->getTotalItemCount();
  }

  /**
   * @return BActiveDataProvider
   */
  public function getOrderParametersDataProvider()
  {
    $criteria = new CDbCriteria();
    $criteria->with = array('param', 'variant');
    $criteria->compare('product_id', $this->history->product_id);
    $criteria->compare('param.key', self::ORDER_PARAMETER_KEY);

    return new BActiveDataProvider('BProductParam', array('criteria' => $criteria));
  }
}