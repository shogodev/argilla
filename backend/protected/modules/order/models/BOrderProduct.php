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
 */
class BOrderProduct extends BActiveRecord
{
  public function relations()
  {
    return array('history' => array(self::HAS_ONE, 'BOrderProductHistory', 'order_product_id'));
  }

  /**
   * @return BOrderProductItem[]
   */
  public function getItems()
  {
    return BOrderProductItem::model()->findAllByAttributes(array('order_product_id' => $this->id));
  }

  public function getFullName()
  {
    $name = array($this->name);
    if( !empty($this->history->articul) )
      array_push($name, "(Артикул: {$this->history->articul})");

    return implode(' ', $name);
  }
}