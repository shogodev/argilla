<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.product
 *
 * @method static BProductParamName model(string $class = __CLASS__)
 *
 * @property string $id
 * @property integer $parent
 * @property integer $position
 * @property integer $visible
 * @property string $name
 * @property string $type
 * @property string $key
 * @property integer $product
 * @property integer $section
 */
class BProductParamName extends BActiveRecord
{
  CONST ROOT_ID = 1;

  /**
   * @var integer $section_id Привязка параметра к дереву каталога
   */
  public $section_id;

  /**
   * @var mixed $value Значение параметров
   */
  public $value;

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

  public function rules()
  {
    return array(
      array('name, type', 'required'),
      array('parent, position, visible, product, section, group', 'numerical', 'integerOnly' => true),
      array('name', 'length', 'max' => 1024),
      array('type, key', 'length', 'max' => 50),
      array('notice', 'safe'),
      array('section_id, type_id', 'safe', 'on' => 'search'),
    );
  }

  public function behaviors()
  {
    return array(
      'uploadBehavior' => array('class' => 'UploadBehavior', 'validAttributes' => 'img'),
    );
  }

  public function relations()
  {
    return array(
      'assignment' => array(self::HAS_ONE, 'BProductParamAssignment', 'param_id'),
      'children'   => array(self::HAS_MANY, 'BProductParamName', 'parent', 'order' => 'position'),
      'variants'   => array(self::HAS_MANY, 'BProductParamVariant', 'param_id'),
    );
  }

  public function isGroup()
  {
    return intval($this->parent) === self::ROOT_ID;
  }

  public function attributeLabels()
  {
    return CMap::mergeArray(parent::attributeLabels(), array(
      'type' => 'Тип параметра',
      'product' => 'Выводить в карточке товара',
      'section' => 'Выводить на разводной',
      'variants' => 'Варианты',
    ));
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
   * @return CActiveDataProvider
   */
  public function search()
  {
    $criteria           = new CDbCriteria;
    $criteria->together = true;
    $criteria->with     = array('assignment', 'variants');

    $criteria->compare('t.id', '<>'.self::ROOT_ID);
    $criteria->compare('parent', '='.self::ROOT_ID);
    $criteria->order = 'position';

    $condition  = '(assignment.section_id IS NULL OR assignment.section_id = 0)';

    if( $this->section_id )
    {
      $condition .= ' OR assignment.section_id = :section_id';
      $criteria->params[':section_id'] = $this->section_id;
    }

    $criteria->addCondition($condition);

    return $this->buildParams($criteria);
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
      $params[] = $group;
      foreach($group->children as $children)
        $params[] = $children;
    }

    return new CArrayDataProvider($params, array(
      'pagination' => false
    ));
  }
}