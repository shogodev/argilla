<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.product
 *
 *
 * @property integer $id
 * @property integer $product_id
 * @property string $param_id
 * @property string $value
 */
class BFacetedSearch extends CActiveRecord
{
  public function tableName()
  {
    return '{{faceted_search}}';
  }

  /**
   * @param string $className
   *
   * @return static BFacetedSearch
   */
  public static function model($className = __CLASS__)
  {
    return parent::model(get_called_class());
  }

}