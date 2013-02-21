<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.product
 *
 * @method static BProductParamVariant model(string $class = __CLASS__)
 *
 * @property string $id
 * @property string $param_id
 * @property string $name
 *
 * @property BProductParamName $param
 */
class BProductParamVariant extends BActiveRecord
{
  public function rules()
  {
    return array(
      array('param_id, name', 'required'),
      array('param_id', 'length', 'max' => 10),
    );
  }

  public function relations()
  {
    return array(
      'param' => array(self::BELONGS_TO, 'BProductParamName', 'param_id'),
    );
  }
}