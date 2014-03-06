<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.product
 *
 * @property string $param_id
 * @property string $section_id
 * @property string $type_id
 * @property string $category_id
 * @property string $collection_id
 */
class ProductParameterAssignment extends FActiveRecord
{
  public function tableName()
  {
    return '{{product_param_assignment}}';
  }
}