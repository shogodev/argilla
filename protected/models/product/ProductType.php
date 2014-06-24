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
 */
class ProductType extends FActiveRecord
{
  public function tableName()
  {
    return '{{product_type}}';
  }

  public function defaultScope()
  {
    $alias = $this->getTableAlias(false, false);

    return array(
      'order' => $alias.'.position',
    );
  }

  public function getMenu()
  {
    /**
     * @var ProductType[] $types
     */
    $menu = array();
    $types = $sections = ProductAssignment::model()->getModels('ProductType', new CDbCriteria());

    foreach($types as $type)
      $menu[$type->id] = array(
        'id' => $type->id,
        'label' => $type->name,
        'url' => array('product/type', 'type' => $type->url));

    return $menu;
  }
}
