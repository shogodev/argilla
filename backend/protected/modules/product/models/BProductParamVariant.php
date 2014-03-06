<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.product.models
 *
 * @method static BProductParamVariant model(string $class = __CLASS__)
 *
 * @property string $id
 * @property string $param_id
 * @property string $name
 * @property string $position
 *
 * @property BProductParamName $param
 */
class BProductParamVariant extends BActiveRecord
{
  public function rules()
  {
    return array(
      array('param_id, name', 'required'),
      array('param_id, position', 'length', 'max' => 10),
      array('notice', 'length', 'max' => 255),
    );
  }

  public function relations()
  {
    return array(
      'param' => array(self::BELONGS_TO, 'BProductParamName', 'param_id'),
    );
  }

  public function getImage()
  {
    $path = 'f/upload/images/color/'.$this->param_id.'_'.$this->id.'.png';

    return file_exists('../'.$path) ? '/'.$path : '/i/sp.gif';
  }

  public function getAlt()
  {
    return $this->param_id.'_'.$this->id.'.png';
  }
}