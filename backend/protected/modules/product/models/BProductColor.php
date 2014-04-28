<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.product
 *
 * @method static BProductColor model(string $class = __CLASS__)
 *
 * @property integer $id
 * @property integer $position
 * @property integer $product_id
 * @property integer $color_type_id
 * @property integer $color_id
 *
 * @property BProduct $product
 * @property BColor $color
 * @property BProductColorType $colorType
 */
class BProductColor extends BActiveRecord
{
  public function rules()
  {
    return array(
      array('color_id, product_id, color_type_id', 'required'),
      array('position, visible', 'numerical', 'integerOnly' => true),
    );
  }

  public function relations()
  {
    return array(
      'color' => array(self::BELONGS_TO, 'BColor', 'color_id'),
      'colorType' => array(self::BELONGS_TO, 'BProductColorType', 'color_type_id'),
      'product' => array(self::BELONGS_TO, 'BProduct', 'product_id'),
    );
  }

  public function attributeLabels()
  {
    return CMap::mergeArray(parent::attributeLabels(), array(
      'color_id' => 'Цвет',
      'color_type_id' => 'Тип цвета'
    ));
  }

  public function afterSave()
  {
    if( $this->scenario == 'copy' )
      return;

    $this->product->updateColorParameters();
  }

  protected function afterDelete()
  {
    $this->product->updateColorParameters();
    parent::afterDelete();
  }
}