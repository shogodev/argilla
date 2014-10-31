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
 * @property FActiveImage $image
 * @property ProductCollection[] $collections
 *
 * @method static ProductCategory model(string $class = __CLASS__)
 */
class ProductCategory extends FActiveRecord
{
  public function behaviors()
  {
    return array(
      'imageBehavior' => array('class' => 'SingleImageBehavior', 'path' => 'product'),
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

  public function relations()
  {
    return array(
      'assignment' => array(self::HAS_MANY, 'ProductTreeAssignment', 'dst_id', 'condition' => 'dst = "category"'),
      'collections' => array(self::HAS_MANY, 'ProductCollection', array('src_id' => 'id'), 'through' => 'assignment', 'condition' => 'src = "collection"'),
    );
  }
}