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
 * @property string  $notice
 *
 * @property ProductSection $section
 * @property FActiveImage $image
 *
 * @method static ProductType model(string $class = __CLASS__)
 */
class ProductType extends FActiveRecord
{
  public function defaultScope()
  {
    $alias = $this->getTableAlias(false, false);

    return array(
      'order' => "IF($alias.position=0, 99999, $alias.position), $alias.name",
    );
  }

  public function behaviors()
  {
    return array(
      'imageBehavior' => array('class' => 'SingleImageBehavior', 'path' => 'product'),
    );
  }

  public function relations()
  {
    return array(
      'assignment' => array(self::HAS_ONE, 'ProductTreeAssignment', 'src_id', 'condition' => 'src="type"'),
      'section' => array(self::HAS_ONE, 'ProductSection', 'dst_id', 'condition' => 'dst="section"', 'through' => 'assignment'),
    );
  }

  public function getMenu(CDbCriteria $criteria = null)
  {
    /**
     * @var ProductType[] $types
     */
    $menu = array();
    $types = ProductAssignment::model()->getModels('ProductType', $criteria);

    foreach($types as $type)
    {
      $menu[$type->id] = array(
        'id' => $type->id,
        'label' => $type->name,
        'url' => array('product/type', 'type' => $type->url),
      );
    }

    return $menu;
  }
}
