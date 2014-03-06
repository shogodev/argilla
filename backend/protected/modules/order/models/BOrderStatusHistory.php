<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.order.models
 *
 * @method static BOrderStatusHistory model(string $class = __CLASS__)
 *
 * @property integer $id
 * @property integer $order_id
 * @property integer $user_id
 * @property integer $date
 * @property integer $old_status_id
 * @property integer $new_status_id
 */
class BOrderStatusHistory extends BActiveRecord
{
  public function relations()
  {
    return array(
      'user' => array(self::BELONGS_TO, 'BUser', 'user_id'),
      'old_status' => array(self::BELONGS_TO, 'BOrderStatus', 'old_status_id'),
      'new_status' => array(self::BELONGS_TO, 'BOrderStatus', 'new_status_id'),
    );
  }
  /**
   * @param BOrder $order
   * @param BOrderStatus $newStatus
   */
  public function add($order, $newStatus)
  {
    $history = new self;

    $history->order_id      = $order->getPrimaryKey();
    $history->user_id       = Yii::app()->user->id;
    $history->old_status_id = $order->status_id;
    $history->new_status_id = $newStatus->id;

    $history->insert();
  }
}