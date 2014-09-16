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
 * @property bool   $visible
 */
class OrderDeliveryType extends FActiveRecord
{
  const SELF_DELIVERY = 1;

  const DELIVERY = 2;

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
}