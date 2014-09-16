<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.order.payment
 *
 * @method static PlatronPaymentType model(string $className = __CLASS__)
 *
 * @property integer $id
 * @property string  $name
 * @property integer $position
 * @property string  $notice
 * @property string  $img
 * @property bool    $visible
 */
class PlatronPaymentType extends FActiveRecord
{
  public $path = '/f/upload/images/payments/';

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

  public function getLabel()
  {
    return implode(" ", array($this->name, $this->notice, $this->img ? CHtml::image($this->path.$this->img) : ''));
  }
}