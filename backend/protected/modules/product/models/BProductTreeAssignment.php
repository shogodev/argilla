<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.product.models
 *
 * @method static BProductTreeAssignment model(string $class = __CLASS__)
 *
 * @property string $src
 * @property integer $src_id
 * @property string $dst
 * @property integer $dst_id
 */
class BProductTreeAssignment extends BActiveRecord
{
  public function rules()
  {
    return array(
      array('dst, dst_id, src, src_id', 'required'),
      array('dst_id, src_id', 'length', 'max' => 10),
    );
  }
}