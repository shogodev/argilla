<?php

/**
 * @property string $id
 * @property string $param_id
 * @property string $name
 *
 * The followings are the available model relations:
 * @property ProductParamName $param
 */
class ProductParamVariant extends FActiveRecord
{
  public function tableName()
  {
    return '{{product_param_variant}}';
  }

  public function relations()
  {
    return array(
      'param' => array(self::BELONGS_TO, 'ProductParamName', 'param_id'),
    );
  }
}