<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.product.models
 */
/**
 * Class BProductParamAssignment
 *
 * @method static BProductParamAssignment model(string $class = __CLASS__)
 *
 * @property string $param_id
 * @property string $section_id
 * @property string $type_id
 * @property string $collection_id
 * @property BProductSection $section
 */
class BProductParamAssignment extends BActiveRecord
{
  public function rules()
  {
    return array(
      array('param_id, section_id, type_id, collection_id', 'length', 'max' => 10),
    );
  }

  public function relations()
  {
    return array(
      'section' => array(self::BELONGS_TO, 'BProductSection', 'section_id')
    );
  }

  public function attributeLabels()
  {
    return CMap::mergeArray(parent::attributeLabels(), array(

    ));
  }
}