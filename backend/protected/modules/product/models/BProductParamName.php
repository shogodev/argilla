<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.product.models
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
 * @property integer $selection
 *
 * @property BProductParamVariant[] $variants
 *
 * @method BProductParamName groups()
 * @property BProductParamAssignment $assignment
 */
class BProductParamName extends BActiveRecord
{
  const ROOT_ID = 1;

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
      array('parent, position, visible, product, section, section_list, selection', 'numerical', 'integerOnly' => true),
      array('name, notice', 'length', 'max' => 1024),
      array('type, key', 'length', 'max' => 50),
      array('notice', 'safe'),
      array('section_id, type_id', 'safe', 'on' => 'search'),
    );
  }

  public function behaviors()
  {
    return array(
      'uploadBehavior' => array('class' => 'UploadBehavior', 'validAttributes' => 'img'),
      'facetedParameterBehavior' => array('class' => 'BFacetedParameterBehavior'),
    );
  }

  public function relations()
  {
    return array(
      'assignment' => array(self::HAS_ONE, 'BProductParamAssignment', 'param_id'),
      'children'   => array(self::HAS_MANY, 'BProductParamName', 'parent', 'order' => 'position'),
      'variants'   => array(self::HAS_MANY, 'BProductParamVariant', 'param_id', 'order' => 'p.position', 'alias' => 'p'),
    );
  }

  public function scopes()
  {
    $alias = $this->getTableAlias();

    return array(
      'groups' => array(
        'condition' => $alias.'.parent='.self::ROOT_ID.' AND id!='.self::ROOT_ID,
      ),
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
      'section' => 'На разводной в режиме "плитка"',
      'section_list' => 'На разводной в режиме "список"',
      'variants' => 'Варианты',
      'parent' => 'Группа',
      'selection' => 'Участвует в подборе',
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
   * @param CDbCriteria $criteria
   *
   * @return BActiveDataProvider|CArrayDataProvider
   */
  public function search(CDbCriteria $criteria = null)
  {
    if( !isset($criteria) )
      $criteria = new CDbCriteria();

    $this->onBeforeSearch(new CEvent($this, array('criteria' => $criteria)));

    $criteria = new CDbCriteria;
    $criteria->together = true;
    $criteria->with = array('assignment', 'variants');
    $criteria->order = 't.position';

    $assignmentCondition = '';
    switch($this->section_id)
    {
      case '':
      break;

      case 'common':
        $assignmentCondition .= '(assignment.section_id IS NULL OR assignment.section_id = 0)';
      break;

      default:
        $assignmentCondition .= ' assignment.section_id = :section_id';
        $criteria->params[':section_id'] = $this->section_id;
      break;
    }

    $criteria->compare('t.id', '<>'.self::ROOT_ID);
    $criteria->compare('parent', '='.self::ROOT_ID);
    if( !empty($assignmentCondition) )
      $criteria->addCondition($assignmentCondition);

    return $this->buildParams($criteria);
  }

  public function getSectionName()
  {
    if( $this->isGroup() )
    {
      if( isset($this->assignment) && isset($this->assignment->section) )
        return $this->assignment->section->name;

      if( empty($this->assignment) )
        return '[Общий]';
    }

    return '';
  }

  public function getRelatedItems()
  {
    switch($this->key)
    {
      case 'color':
        $config = array(
          'position' => array('class' => 'span1'),
          'name',
          'notice' => array('tag' => 'image', 'label' => 'Изображение'),
        );
        break;

      case 'url':
        $config = array(
          'position' => array('class' => 'span1'),
          'name',
          'notice' => array('label' => 'Url'),
        );
        break;

      default:
        $config = array(
          'position' => array('class' => 'span1'),
          'name'
        );
    }

    return $config;
  }

  /**
   * @param CDbCriteria $criteria
   *
   * @return CArrayDataProvider
   */
  public function buildParams(CDbCriteria $criteria)
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