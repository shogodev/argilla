<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.product
 */

/**
 * @method static ProductOption model(string $class = __CLASS__)
 *
 * @property integer $id
 * @property integer $position
 * @property string $articul
 * @property integer $product_id
 * @property string $name
 * @property string $price
 * @property string $content
 * @property string $visible
 *
 * @property string $image
 * @property Product $product
 *
 * @mixin FCollectionElementBehavior
 */
class ProductOption extends FActiveRecord
{
  public function behaviors()
  {
    return array(
      'imageBehavior' => array('class' => 'SingleImageBehavior', 'path' => 'product'),
      'collectionElement' => array('class' => 'FCollectionProductOptionBehavior'),
    );
  }

  public function defaultScope()
  {
    $alias = $this->getTableAlias(false, false);

    return array(
      'condition' => $alias.'.visible=1',
      'order' => $alias.'.position',
    );
  }
}