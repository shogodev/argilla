<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.product
 *
 * @method static BColor model(string $class = __CLASS__)
 *
 * @property integer $id
 * @property integer $category_id
 * @property integer $variant_id
 * @property integer $color_type_id
 * @property string  $name
 * @property string  $img
 *
 * @property BProductColorType $colorType
 */
class BColor extends BActiveRecord
{
  public function behaviors()
  {
    return array('uploadBehavior' => array('class' => 'UploadBehavior', 'validAttributes' => 'img'));
  }

  public function rules()
  {
    return array(
      array('name, color_type_id', 'required'),
      array('category_id, variant_id', 'numerical', 'integerOnly' => true),
      array('name', 'length', 'max' => 255),
      array('variant_id', 'length', 'max' => 10),
    );
  }

  public function relations()
  {
    return array(
      'category' => array(self::BELONGS_TO, 'BProductCategory', 'category_id'),
      'colorType' => array(self::BELONGS_TO, 'BProductColorType', 'color_type_id'),
      'variant' => array(self::BELONGS_TO, 'BProductParamVariant', 'variant_id', 'on' => 'param_id='.BProductParamName::COLOR_ID),
    );
  }

  public function attributeLabels()
  {
    return Cmap::mergeArray(parent::attributeLabels(), array(
      'variant_id' => 'Группа цвета',
      'color_type_id' => 'Тип цвета',
    ));
  }

  public function getVariants()
  {
    return BProductParamVariant::model()->findAllByAttributes(array('param_id' => BProductParamName::COLOR_ID));
  }

  public function getImage()
  {
    return $this->img ? CHtml::image(Yii::app()->request->hostInfo."/f/product/".$this->img, "", array("class" => "small_img")) : null;
  }

  protected function getSearchCriteria(CDbCriteria $criteria)
  {
    $criteria->compare('category_id', $this->category_id);
    $criteria->compare('variant_id', $this->variant_id);
    $criteria->compare('color_type_id', $this->color_type_id);
    $criteria->compare('name', $this->name, true);

    return $criteria;
  }
}