<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.product
 *
 * @property string  $id
 * @property integer $position
 * @property string  $url
 * @property string  $name
 * @property string  $img
 * @property string  $notice
 * @property integer $visible
 *
 * @method static ProductCategory model(string $class = __CLASS__)
 */
class ProductCategory extends FActiveRecord
{
  protected $products;

  public function defaultScope()
  {
    $alias = $this->getTableAlias(false, false);

    return array(
      'condition' => $alias.'.visible=1',
      'order' => $alias.'.position',
    );
  }
}