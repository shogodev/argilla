<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 *
 * @method static BOrderDeliveryType model(string $class = __CLASS__)
 *
 * @property string $id
 * @property string $name
 * @property integer $position
 * @property string $price
 * @property string $notice
 * @property integer $visible
 *
 * @property BOrder[] $orders
 */
class BOrderDeliveryType extends BActiveRecord
{
  public function rules()
  {
    return array(
      array('name, price', 'required'),
      array('position, visible', 'numerical', 'integerOnly' => true),
      array('name', 'length', 'max' => 128),
      array('price', 'length', 'max' => 10),
      array('notice', 'safe'),
      array('id, name, position, price, notice, visible', 'safe', 'on' => 'search'),
    );
  }

  public function relations()
  {
    return array(
      'orders' => array(self::HAS_MANY, 'BOrder', 'delivery_id'),
    );
  }

  public function attributeLabels()
  {
    return CMap::mergeArray(parent::attributeLabels(), array(
      'name' => 'Name',
      'position' => 'Position',
      'price' => 'Price',
      'notice' => 'Notice',
      'visible' => 'Visible',
    ));
  }

  /**
  * @param CDbCriteria $criteria
  *
  * @return CDbCriteria
  */
  protected function getSearchCriteria(CDbCriteria $criteria)
  {
    $criteria->compare('id', $this->id, true);
    $criteria->compare('name', $this->name, true);
    $criteria->compare('position', $this->position);
    $criteria->compare('price', $this->price, true);
    $criteria->compare('notice', $this->notice, true);
    $criteria->compare('visible', $this->visible);

    return $criteria;
  }
}