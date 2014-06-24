<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.product
 *
 * @method static BProductImg model(string $class = __CLASS__)
 *
 * @property integer $id
 * @property integer $parent
 * @property string $name
 * @property string $notice
 * @property string $position

 * @property BProduct $product
 */
class BProductImg extends BActiveRecord
{
  public function rules()
  {
    return array(
      array('parent, name', 'required'),
      array('position', 'numerical', 'integerOnly' => true),
      array('name, notice', 'length', 'max' => 255),

      array('notice', 'safe', 'on' => 'search')
    );
  }

  public function relations()
  {
    return array(
      'product' => array(self::BELONGS_TO, 'BProduct', 'parent'),
    );
  }

  public function getSearchCriteria(CDbCriteria $criteria)
  {
    $criteria->compare('parent', $this->parent);

    return $criteria;
  }
}