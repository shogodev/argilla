<?php

/**
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

  public function getTypes()
  {
    $product    = Product::model()->tableName();
    $assignment = ProductAssignment::model()->tableName();

    $criteria            = new CDbCriteria();
    $criteria->select    = 't.id, t.name, t.notice, t.url';
    $criteria->join      = 'JOIN '.$assignment.' AS a ON t.id = a.type_id ';
    $criteria->join     .= 'JOIN '.$product.' AS p ON p.id = a.product_id';
    $criteria->condition = 'p.visible=1';
    $criteria->distinct  = true;

    return $this->findAll($criteria);
  }

  public function getMenu()
  {
    $menu[] = array('label' => 'Разделы');

    $types = $this->getTypes();

    foreach($types as $type)
      $menu[$type->id] = array(
        'id' => $type->id,
        'label' => $type->name,
        'url' => array('product/type', 'type' => $type->url));

    return $menu;
  }
}
