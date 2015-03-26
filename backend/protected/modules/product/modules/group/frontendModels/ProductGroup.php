<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.product
 *
 * @method static ProductGroup model(string $class = __CLASS__)
 *
 * @property integer $id
 * @property integer $position
 * @property string $name
 * @mixin AssociationBehavior
 */
class ProductGroup extends FActiveRecord
{
  public function behaviors()
  {
    return array(
      'associationBehavior' => array('class' => 'AssociationBehavior')
    );
  }
}