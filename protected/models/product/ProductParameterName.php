<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.product
 */

/**
 * Class ProductParameterName
 *
 * @method static ProductParameterName model(string $class = __CLASS__)
 * @property integer $id
 * @property integer $parent
 * @property integer $position
 * @property integer $visible
 * @property string $name
 * @property string $notice
 * @property string $type
 * @property string $key
 * @property integer $product
 * @property integer $section
 * @property integer $section_line
 * @property integer $selection
 * @property CDbCriteria $groupCriteria
 * @property ProductParameterName $group
 * @property ProductParameterVariant[] $values
 * @property ProductParameterVariant[] $variants
 * @property integer $productId
 * @property string $value
 *
 * @property ProductParameterVariant $variant
 */
class ProductParameterName extends FActiveRecord
{
  const TYPE_TEXT = 'text';

  const TYPE_SLIDER = 'slider';

  const TYPE_CHECKBOX = 'checkbox';

  const TYPE_SELECT = 'select';

  const TYPE_RADIO = 'radio';

  /**
   * @var ProductParameterVariant[]
   */
  public $values = array();

  /**
   * @var CDbCriteria $groupCriteria
   */
  protected $groupCriteria;

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
      'condition' => "{$alias}.parent = 1 OR ({$alias}.parent != 1 AND {$alias}.visible = 1)",
      'order' => $alias.'.position'
    );
  }

  /**
   * @return integer
   */
  public function getRootId()
  {
    return $this->findBySql("SELECT * FROM ".$this->dbConnection->schema->quoteTableName($this->tableName())." WHERE parent = id")->id;
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
   *
   * @return $this
   */
  public function setGroupCriteria(CDbCriteria $criteria)
  {
    $this->groupCriteria = $criteria;

    return $this;
  }

  /**
   * @param CDbCriteria|null $criteria
   *
   * @return ProductParameterName[]
   */
  public function search(CDbCriteria $criteria = null)
  {
    $groupCriteria = $this->getGroupCriteria();
    $groupCriteria->compare('parent', $this->getRootId());
    $groupCriteria->select = 'id';
    $assignmentTable = ProductParameterAssignment::model()->tableName();
    $groupCriteria->join = 'LEFT OUTER JOIN '.$this->dbConnection->schema->quoteTableName($assignmentTable).' AS assignment ON assignment.param_id = t.id';

    $builder = new CDbCommandBuilder(Yii::app()->db->getSchema());
    $command = $builder->createFindCommand($this->tableName(), $groupCriteria);

    $parameterNames = array();
    if( $groupProductParameterNameIds = $command->queryColumn() )
    {
      $criteria = isset($criteria) ? $criteria : new CDbCriteria();
      $criteria->with = CMap::mergeArray($criteria->with, array('group', 'variants'));
      $criteria->addInCondition('t.parent', $groupProductParameterNameIds);

      $parameterNames = $this->findAll($criteria);
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
      $this->getGroupCriteria()->addCondition(
        "assignment.{$key} = :{$key}
        OR assignment.{$key} IS NULL
        OR assignment.{$key} = 0"
      );

      $this->getGroupCriteria()->params[':'.$key] = $value;
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
    return array_reduce($this->parameters, function ($result, $item)
    {
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
    return array_reduce($this->parameters, function ($result, $item) use ($parameterId)
    {
      return $parameterId == $item->id ? $item : $result;
    });
  }

  /**
   * @param ProductParameterVariant[] $variants
   */
  public function setVariants(array $variants)
  {
    $sortedParameters = array();

    foreach($variants as $variant)
    {
      if( isset($this->parameters[$variant->id]) )
      {
        $sortedParameters[$variant->id] = $this->parameters[$variant->id];
        $this->values[$variant->id] = $variant;
      }
    }

    foreach($this->parameters as $variantId => $parameter)
    {
      if( !isset($sortedParameters[$variantId]) )
        $sortedParameters[$variantId] = $parameter;
    }

    $this->parameters = $sortedParameters;
  }

  /**
   * @return string
   */
  public function getGroupKey()
  {
    $group = $this->group;

    return $group ? $this->group->key : null;
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
        return implode(", ", array_reduce($this->getValues(),
          function ($result, $item)
          {
            $result[] = $item['name'];

            return $result;
          }, array()
        ));
    }
  }

  /**
   * @return ProductParameterVariant
   */
  public function getVariant()
  {
    return !empty($this->parameters) ? Arr::reset($this->parameters)->variant : new ProductParameterVariant();
  }

  public function getImage()
  {
    return 'f/upload/images/param_'.$this->id.'.png';
  }

  /**
   * @return ProductParameter|null
   */
  public function getParameter()
  {
    return Arr::reset($this->getParameters());
  }
}