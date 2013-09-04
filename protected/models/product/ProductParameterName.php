<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.product
 *
 * @property integer $id
 * @property integer $parent
 * @property integer $position
 * @property integer $visible
 * @property string $name
 * @property string $type
 * @property string $key
 * @property integer $product
 * @property integer $section
 *
 * @property CDbCriteria $groupCriteria
 * @property ProductParameterName $group
 * @property ProductParameterVariant[] $values
 * @property ProductParameterVariant[] $variants
 * @property integer $productId
 * @property string $value
 *
 * @method static ProductParameterName model(string $class = __CLASS__)
 */
class ProductParameterName extends FActiveRecord
{
  const TYPE_TEXT = 'text';

  const TYPE_SLIDER = 'slider';

  const TYPE_CHECKBOX = 'checkbox';

  const TYPE_SELECT = 'select';

  const TYPE_RADIO = 'radio';

  /**
   * @var CDbCriteria
   */
  protected $groupCriteria;

  /**
   * @var ProductParameterVariant[]
   */
  protected $values = array();

  /**
   * @var ProductParameter[]
   */
  protected $parameters = array();

  protected $productId;

  public function tableName()
  {
    return '{{product_param_name}}';
  }

  public function relations()
  {
    return array(
      'group' => array(self::BELONGS_TO, 'ProductParameterName', 'parent'),
      'assignment' => array(self::HAS_ONE, 'ProductParameterAssignment', 'param_id'),
      'variants' => array(self::HAS_MANY, 'ProductParameterVariant', 'param_id'),
    );
  }

  public function defaultScope()
  {
    $alias = $this->getTableAlias(false, false);

    return array(
      'condition' => $alias.'.visible=1',
      'order' => $alias.'.position'
    );
  }

  /**
   * @return integer
   */
  public function getRootId()
  {
    return $this->findBySql("SELECT * FROM ".$this->tableName()." WHERE parent = id")->id;
  }

  /**
   * @return CDbCriteria
   */
  public function getGroupCriteria()
  {
    if( $this->groupCriteria === null )
      $this->groupCriteria = new CDbCriteria();

    return $this->groupCriteria;
  }

  /**
   * @param CDbCriteria $criteria
   * @return $this
   */
  public function setGroupCriteria(CDbCriteria $criteria)
  {
    $this->groupCriteria = $criteria;
    return $this;
  }

  /**
   * @return ProductParameterName[]
   */
  public function search()
  {
    $parameterNames  = array();
    $assignmentTable = ProductParameterAssignment::model()->tableName();

    $this->groupCriteria->compare('parent', $this->getRootId());
    $this->groupCriteria->select = 'id';
    $this->groupCriteria->join   = 'LEFT OUTER JOIN '.$assignmentTable.' AS assignment ON assignment.param_id = t.id';

    $builder = new CDbCommandBuilder(Yii::app()->db->getSchema());
    $command = $builder->createFindCommand($this->tableName(), $this->groupCriteria);

    if( $nameIds = $command->queryColumn() )
    {
      $nameCriteria = new CDbCriteria();
      $nameCriteria->with = array('group');
      $nameCriteria->addInCondition('t.parent', $nameIds);

      $parameterNames = $this->findAll($nameCriteria);
    }

    return $parameterNames;
  }

  /**
   * Добавляем к запросу условия выборки параметров по привязанным разделам
   *
   * @param $assignedParameters
   */
  public function addAssignmentCondition($assignedParameters)
  {
    foreach($assignedParameters as $key => $value)
    {
      $this->groupCriteria->addCondition(
        "assignment.{$key} = :{$key}
        OR assignment.{$key} IS NULL
        OR assignment.{$key} = 0"
      );

      $this->groupCriteria->params[':'.$key] = $value;
    }
  }

  /**
   * @param integer $productId
   */
  public function setProductId($productId)
  {
    $this->productId = $productId;
  }

  /**
   * @return integer
   */
  public function getProductId()
  {
    return $this->productId;
  }

  /**
   * @param ProductParameter[] $parameters
   */
  public function setParameters(array $parameters)
  {
    $this->parameters = $parameters;
  }

  public function getParameters()
  {
    return $this->parameters;
  }

  /**
   * @param ProductParameterVariant[] $values
   */
  public function setValues(array $values)
  {
    $this->values = $values;
  }

  /**
   * @return ProductParameterVariant[]
   */
  public function getValues()
  {
    return $this->values;
  }

  /**
   * @return array
   */
  public function getVariantKeys()
  {
    return array_keys($this->parameters);
  }

  /**
   * @return array
   */
  public function getParameterKeys()
  {
    return array_reduce($this->parameters, function($result, $item){
      $result[] = $item['id'];
      return $result;
    }, array());
  }

  public function getParameterKeysByVariantId($id)
  {
    foreach($this->parameters as $parameter)
    {
      if( $parameter->variant_id == $id )
        return $parameter->id;
    }

    return null;
  }

  public function getParameterById($parameterId)
  {
    return array_reduce($this->parameters, function($result, $item) use($parameterId) {
      return $parameterId == $item->id ? $item : $result;
    });
  }

  /**
   * @param ProductParameterVariant[] $variants
   */
  public function setVariants(array $variants)
  {
    foreach($this->parameters as $i => $value)
    {
      if( isset($variants[$i]) )
      {
        $this->values[$i] = $variants[$i];
      }
    }
  }

  /**
   * @return string
   */
  public function getGroupKey()
  {
    return $this->group->key;
  }

  /**
   * @return string
   */
  public function getValue()
  {
    switch($this->type)
    {
      case self::TYPE_TEXT:
      case self::TYPE_SLIDER:
        return Arr::reset($this->parameters)['value'];

      default:
        return implode(", ", array_reduce($this->getValues(), function($result, $item){
          $result[] = $item['name'];
          return $result;
        }, array()));
    }
  }
}