<?php
/**
 * @author Vladimir Utenkov <utenkov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.order
 *
 * @method static OrderPaymentType model(string $className = __CLASS__)
 *
 * @property int $id
 * @property string $name
 * @property int $position
 * @property string $notice
 * @property bool $visible
 */
class OrderPaymentType extends FActiveRecord
{
  const CASH = 1;

  const NON_CASH = 2;

  const E_PAY = 3;

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