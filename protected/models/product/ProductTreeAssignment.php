<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.product
 *
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