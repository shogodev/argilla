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
 * @property integer $variant_id
 * @property string  $name
 * @property string  $img
 */
class BColor extends BActiveRecord
{
  const COLOR_PARAMETER_ID = 6;

  public function behaviors()
  {
    return array('uploadBehavior' => array('class' => 'UploadBehavior', 'validAttributes' => 'img'));
  }

  public function rules()
  {
    return array(
      array('name', 'required'),
      array('name', 'length', 'max' => 255),
      array('variant_id', 'filter', 'filter' => function($value){ return empty($value) ? null : $value;}),
      array('variant_id', 'safe'),
    );
  }

  public function relations()
  {
    return array(
      'variant' => array(self::BELONGS_TO, 'BProductParamVariant', 'variant_id', 'on' => 'param_id='.self::COLOR_PARAMETER_ID),
    );
  }

  public function attributeLabels()
  {
    return Cmap::mergeArray(parent::attributeLabels(), array(
      'variant_id' => 'Группа цвета',
    ));
  }

  public function getVariants()
  {
    return BProductParamVariant::model()->findAllByAttributes(array('param_id' => self::COLOR_PARAMETER_ID));
  }

  public function getImage()
  {
    return $this->img ? CHtml::image(Yii::app()->request->hostInfo."/f/product/color/".$this->img, "", array("class" => "small_img")) : null;
  }

  protected function getSearchCriteria(CDbCriteria $criteria)
  {
    $criteria->compare('variant_id', $this->variant_id);
    $criteria->compare('name', $this->name, true);

    return $criteria;
  }
}