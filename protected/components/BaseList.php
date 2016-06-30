<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>, Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 *
 * @property CDbCriteria $criteria
 */
class BaseList extends CComponent
{
  /**
   * @var string $sorting
   */
  public $sorting;

  /**
   * @var bool $pagination
   */
  public $pagination;

  /**
   * @var Filter[]|null $filters
   */
  public $filters = null;

  /**
   * Параметр, отвечающий за выборку параметров и картинок товаров,
   * можно отключить, если данные эти не используются, чтобы не тратить время на обработку
   * @var bool
   */
  public $fetchContent = true;

  public static $sortingRange = array();

  /**
   * @var $products FActiveDataProvider
   */
  protected $dataProvider;

  /**
   * @var CDbCriteria $criteria
   */
  protected $criteria;

  /**
   * @var CDbCriteria $criteria
   */
  protected $filteredCriteria;

  /**
   * @param CDbCriteria $criteria
   * @param null $sorting
   * @param bool $pagination
   * @param null $filters
   */
  public function __construct(CDbCriteria $criteria, $sorting = null, $pagination = true, $filters = null)
  {
    $this->sorting = $sorting;
    $this->pagination = $pagination;
    $this->criteria = clone $criteria;

    if( !empty($filters) )
      $this->filters = is_array($filters) ? $filters : array($filters);

    $this->init();
  }

  public function init()
  {
    $this->setOrder();
  }

  /**
   * @param boolean $refresh
   *
   * @return FActiveDataProvider
   */
  public function getDataProvider($refresh = false)
  {
    if( is_null($this->dataProvider) || $refresh )
      $this->buildDataProvider($this->getFilterCriteria());

    return $this->dataProvider;
  }

  /**
   * @return FActiveDataProvider
   */
  public function getRandomDataProvider()
  {
    $criteria = clone $this->getFilterCriteria();
    $criteria->condition = '';
    $criteria->params = $this->getConditionParams($criteria, 'order');

    $modelIds = $this->getModelIds();

    if( $criteria->limit )
    {
      shuffle($modelIds);
      $modelIds = array_slice($modelIds, 0, $criteria->limit);
    }
    $criteria->addInCondition($this->getTablePrefix().'.id', $modelIds);

    return $this->buildDataProvider($criteria);
  }

  /**
   * @param bool $resetLimit
   *
   * @return array
   * @throws CDbException
   */
  public function getModelIds($resetLimit = true)
  {
    $criteria = clone $this->getFilterCriteria();

    if( $resetLimit )
    {
      $criteria->limit = -1;
      $criteria->offset = -1;
    }

    ProductAssignment::model()->addAssignmentCondition($criteria);

    $builder = new CDbCommandBuilder(Yii::app()->db->getSchema());
    $command = $builder->createFindCommand($this->getModel()->tableName(), $criteria);

    return CHtml::listData($command->queryAll(), 'id', 'id');
  }

  public function getCacheKey()
  {
    $arrayForSerialize = array(
      $this->criteria->toArray(),
      $this->sorting,
    );

    if( $this->filters )
      foreach($this->filters as $filter)
        $arrayForSerialize[] = $filter->getState();

    return sha1(serialize($arrayForSerialize));
  }

  public function getFilterCriteria()
  {
    if( $this->filteredCriteria == null )
    {
      $filteredCriteria = clone $this->criteria;

      if( $this->filters )
      {
        foreach($this->filters as $filter)
          $filteredCriteria = $filter->apply($filteredCriteria);

        foreach(array('distinct', 'order') as $value)
        {
          $filteredCriteria->$value = $this->criteria->$value;
          if( $value == 'order' && !empty($this->criteria->join) )
          {
            $filteredCriteria->join = $this->getOrderJoin($filteredCriteria->order, $this->criteria->join);
          }
        }
      }

      $this->filteredCriteria = $filteredCriteria;
    }

    return $this->filteredCriteria;
  }

  /**
   * Возвращает раздельный массив join'ов
   * @param $sql
   *
   * @return array
   * @throws CHttpException
   */
  protected function getJoins($sql)
  {
    $joins = array();
    $sql = strtr($sql, array("\n" => ' ', "\r" => '', '  ' => ' '));
    $sql = trim(preg_replace('/\s+2/', '', $sql));
    if( $data = preg_split('/(CROSS JOIN)|(INNER JOIN)|(LEFT OUTER JOIN)|(LEFT JOIN)|(RIGHT JOIN)|(RIGHT OUTER JOIN)|(JOIN)/i', $sql, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE) )
    {
      if( count($data) % 2 !== 0 )
        throw new CHttpException(500, 'Ошибка!');

      for($i = 0; $i < count($data); $i += 2)
      {
        $joins[] = $data[$i].' '.$data[$i + 1];
      }
    }

    return $joins;
  }

  /**
   * Возвращает join нужный для сортировки
   * @param $order
   * @param $baseJoin
   *
   * @return string
   * @throws CHttpException
   */
  protected function getOrderJoin($order, $baseJoin)
  {
    $orderJoins = array();

    $joins = $this->getJoins($baseJoin);

    if( preg_match_all('/(\w+)\./i', $order, $matches) )
    {
      $tableAliases = array_unique($matches[1]);

      foreach($tableAliases as $alias)
      {
        foreach($joins as $join)
        {
          if( preg_match('/AS\s*(\w+)\s|$/i', $join, $matches) )
          {
            if( $matches[1] == $alias )
              $orderJoins[] = $join;
          }
        }
      }
    }

    return implode(' ', $orderJoins);
  }

  protected function setOrder()
  {
    if( empty($this->criteria->order) )
      $this->criteria->order = Arr::get(static::$sortingRange, $this->sorting, Arr::reset(static::$sortingRange));
  }

  protected function buildDataProvider(CDbCriteria $criteria)
  {
    $config = array('criteria' => $criteria);
    if( !$this->pagination || $this->pagination instanceof FPagination )
      $config['pagination'] = $this->pagination;

    $this->dataProvider = new FActiveDataProvider($this->getModelName(), $config);

    if( $this->fetchContent )
      $this->dataProvider->attachEventHandler('onAfterFetchData', array($this, 'afterFetchData'));

    return $this->dataProvider;
  }

  protected function afterFetchData($event)
  {
    $this->setImages();
  }

  protected function setImages()
  {
    /**
     * @var ActiveImageBehavior $imagesBehavior
     */
    if( $imagesBehavior = $this->getModel()->asa('imagesBehavior') )
    {
      $modelImages = $this->findRecords('parent', $imagesBehavior->imageClass, null, $imagesBehavior->getColorCriteria());

      $images = array();
      foreach($modelImages as $image)
        $images[$image['parent']][$image['type']][] = $image;

      /**
       * @var $product Product
       */
      foreach($this->dataProvider->getData() as $product)
      {
        if( isset($images[$product->id]) )
        {
          foreach($images[$product->id] as $type => $imgs)
          {
            $product->setImages($imgs, $type);
          }
        }
        else
        {
          $product->setImages(array(), null);
        }
      }
    }
  }

  protected function setRelations($relationName)
  {
    $models = array();
    $relation = Product::model()->getMetaData()->relations[$relationName];
    $records = $this->findRecords($relation->foreignKey, $relation->className);

    foreach($records as $record)
    {
      if( get_class($relation) === 'CHasOneRelation' )
      {
        $models[$record['product_id']] = $record;
      }
      else
      {
        $models[$record['product_id']][] = $record;
      }
    }

    $this->setRecords($relationName, $models);
  }

  /**
   * @param string $fk
   * @param string $className
   * @param array $keys
   * @param CDbCriteria $criteria
   *
   * @return FActiveRecord[]
   */
  protected function findRecords($fk, $className, array $keys = null, CDbCriteria $criteria = null)
  {
    if( !isset($criteria) )
    {
      $criteria = new CDbCriteria();
    }

    $criteria->addInCondition($fk, isset($keys) ? $keys : $this->dataProvider->getKeys(true));

    return $className::model()->findAll($criteria);
  }

  /**
   * @param string $modelName
   * @param FActiveRecord[] $models
   */
  protected function setRecords($modelName, array $models)
  {
    /**
     * @var $product Product
     */
    foreach($this->dataProvider->getData() as $product)
    {
      $records = Arr::get($models, $product->id, array(null));

      if( is_object($records) )
      {
        $useIndex = false;
        $records = array($records);
      }

      foreach($records as $model)
      {
        $product->addRelatedRecord($modelName, $model, isset($useIndex) ? $useIndex : true);
      }
    }
  }

  /**
   * @return CActiveRecord
   */
  protected function getModelName()
  {
    return str_replace('List', '', get_class($this));
  }

  protected function getTablePrefix()
  {
    return 't';
  }

  /**
   * @return CActiveRecord
   */
  protected function getModel()
  {
    $modelName = $this->getModelName();
    return $modelName::model();
  }

  /**
   * @param CDbCriteria $criteria
   * @param $attribute
   *
   * @return array
   */
  protected function getConditionParams(CDbCriteria $criteria, $attribute)
  {
    $params = array();

    if( preg_match_all('/(:\w+)/', $criteria->{$attribute}, $matches) )
    {
      foreach($matches[0] as $param)
      {
        $params[$param] = Arr::get($criteria->params, $param);
      }
    }

    return $params;
  }
}