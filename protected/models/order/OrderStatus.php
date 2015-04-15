<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.order
 *
 * @method static OrderStatus model(string $class = __CLASS__)
 *
 * @property integer $id
 * @property string $name
 * @property string $sysname
 */
class OrderStatus extends FActiveRecord
{
  const STATUS_NEW = 1;

  const STATUS_CONFIRMED = 2;

  const STATUS_WAIT_DELIVERY = 3;

  const STATUS_DELIVERED = 4;

  const STATUS_CANCELED = 5;

  public function __toString()
  {
    return $this->name;
  }
}