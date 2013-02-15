<?php

/**
 * @property string $param_id
 * @property string $section_id
 * @property string $type_id
 *
 * The followings are the available model relations:
 * @property ProductParamName $param
 */
class ProductParamAssignment extends FActiveRecord
{
  public function tableName()
  {
    return '{{product_param_assignment}}';
  }

  public function relations()
  {
    return array(
      'param' => array(self::BELONGS_TO, 'ProductParamNames', 'param_id'),
    );
  }
}