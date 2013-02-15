<?php

/**
 * @property string $id
 * @property integer $parent
 * @property integer $position
 * @property integer $visible
 * @property string $name
 * @property string $img
 * @property string $type
 * @property string $key
 */
class ProductParamName extends FActiveRecord
{
  CONST ROOT_ID = 1;

  /**
   * @var int $type_id Привязка параметра к дереву каталога
   */
  public $type_id;

  public $interval = array('min' => 0, 'max' => PHP_INT_MAX);

  /**
   * @var mixed $value Значения параметров
   */
  public $value;

  /**
   * @var int $variant_id Вариант текущего значения параметра
   */
  public $variant_id;

  /**
   * Оличается ли значение параметра от остальных в ProductList при сравнении товаров
   * @var bool
   */
  public $different = false;

  public static function getTypes()
  {
    return array(
      'text'     => 'Текст (text)',
      'slider'   => 'Диапазон (slider)',
      'checkbox' => 'Набор (checkbox)',
      'select'   => 'Селектор (select)',
      'radio'    => 'Радио кнопка (radio)',
    );
  }

  public function tableName()
  {
    return '{{product_param_name}}';
  }

  public function relations()
  {
    return array(
      'assignment' => array(self::HAS_ONE, 'ProductParamAssignment', 'param_id'),
      'children'   => array(self::HAS_MANY, 'ProductParamName', 'parent', 'order' => 'position'),
      'variants'   => array(self::HAS_MANY, 'ProductParamVariant', 'param_id'),
    );
  }

  protected function afterFind()
  {
    $this->img = !empty($this->img) ? 'f/product/'.$this->img : '';
    return parent::afterFind();
  }


  public function isGroup()
  {
    return intval($this->parent) === self::ROOT_ID;
  }

  /**
   * @param null $criteria
   *
   * @return CArrayDataProvider
   */
  public function search($criteria = null)
  {
    if( !isset($criteria) )
    {
      $criteria = new CDbCriteria;
    }

    $criteria->together = true;
    $criteria->with     = array('assignment', 'variants');

    $criteria->compare('t.id', '<>'.self::ROOT_ID);
    $criteria->compare('parent', '='.self::ROOT_ID);
    $criteria->compare('visible', '=1');
    $criteria->order = 'position';

    return $this->buildParams($criteria);
  }

  /**
   * Получаем типы параметров
   *
   * @param array $param_ids
   *
   * @return array
   */
  public function getParameterTypes(array $param_ids)
  {
    $types    = array();
    $criteria = new CDbCriteria();
    $criteria->addInCondition('id', $param_ids);

    $result = self::model()->findAll($criteria);

    foreach($result as $param)
      $types[$param->id] = $param->type;

    return $types;
  }

  /**
   * Устанавливаем значение параметра в соответствии с его типом
   *
   * @param $paramValue
   */
  public function setValue($paramValue)
  {
    $this->variant_id = $paramValue['variant_id'];

    switch($this->type)
    {
      case 'text':
      case 'slider':
      $this->value = $paramValue['value'];
        break;

      case 'radio':
        $this->value = $paramValue['variant_id'];
        break;

      default:
        $this->value[] = $paramValue['variant_id'];
        $this->value = array_unique($this->value);
    }
  }

  public function setVariants($variants)
  {
    $variants = CHtml::listData($variants, 'id', 'name');

    if( is_array($variants) && count($variants) )
    {
      if( !is_array($this->value) )
        $this->value = array($this->value);

      $result = array();

      foreach($this->value as $value)
        $result[] = isset($variants[$value]) ? $variants[$value] : '';

      return implode(", ", $result);
    }
  }

  /**
   * @param CDbCriteria $criteria
   *
   * @return CArrayDataProvider
   */
  protected function buildParams(CDbCriteria $criteria)
  {
    $params = array();
    $groups = $this->findAll($criteria);

    foreach($groups as $group)
    {
      foreach($group->children as $children)
        if( $children->visible )
          $params[] = $children;
    }

    $this->getIntervals($params);

    return new CArrayDataProvider($params, array(
      'pagination' => false
    ));
  }

  /**
   * получаем минимальные и максимальные значения для слайдеров
   *
   * @param $params
   */
  protected function getIntervals(&$params)
  {
    $intervals = array();

    foreach($params as $i => $param)
      if( $param->type === 'slider' )
        $intervals[$param->id] = $i;

    if( !empty($intervals) )
    {
      $data = Yii::app()->db->createCommand()
        ->select('param_id, MAX(value + 0) AS max, MIN(value + 0) AS min')
        ->from(ProductParam::model()->tableName())
        ->join(Product::model()->tableName(), 'product_id = id')
        ->where(array('AND', 'visible=1', array('IN', 'param_id', array_keys($intervals))))
        ->query();

      foreach($data as $row)
      {
        $i = $intervals[$row['param_id']];
        $params[$i]->interval = array('min' => $row['min'], 'max' => $row['max']);
      }
    }
  }
}