<?php
/**
 * @author Vladimir Utenkov <utenkov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.order
 *
 * @method static DirPayment model(string $className = __CLASS__)
 *
 * @property int $id
 * @property string $name
 * @property int $position
 * @property string $notice
 * @property bool $visible
 */
class DirPayment extends FActiveRecord
{
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