<?php
/**
 * @property string $src
 * @property int    $src_id
 * @property string $dst
 * @property int    $dst_id
 */
class ProductTreeAssignment extends FActiveRecord
{
  public function tableName()
  {
    return '{{product_tree_assignment}}';
  }
}