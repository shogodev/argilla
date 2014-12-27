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
 * @property integer $color_id
 * @property integer $visible
 *
 * @property BProduct $product
 * @property BColor $color
 */
class BProductColor extends BActiveRecord
{
  public function rules()
  {
    return array(
      array('color_id, product_id', 'required'),
      array('position, visible', 'numerical', 'integerOnly' => true),
      array('color_id, visible, position', 'safe', 'on' => 'search')
    );
  }

  public function relations()
  {
    return array(
      'color' => array(self::BELONGS_TO, 'BColor', 'color_id'),
      'product' => array(self::BELONGS_TO, 'BProduct', 'product_id'),
    );
  }

  public function attributeLabels()
  {
    return CMap::mergeArray(parent::attributeLabels(), array(
      'color_id' => 'Цвет',
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

  protected function getSearchCriteria(CDbCriteria $criteria)
  {
    $criteria->compare('product_id', $this->product_id);
    $criteria->compare('color_id', $this->color_id);
    $criteria->compare('visible', $this->visible);
    $criteria->compare('position', $this->position);

    return $criteria;
  }
}
