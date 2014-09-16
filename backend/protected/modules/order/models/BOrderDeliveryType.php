<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.order.models
 *
 * @method static BOrderDeliveryType model(string $className = __CLASS__)
 *
 * @property int    $id
 * @property string $name
 * @property int    $position
 * @property string $notice
 * @property bool   $visible
 */
class BOrderDeliveryType extends BActiveRecord
{
  const SELF_DELIVERY = 1;

  const DELIVERY = 2;
}