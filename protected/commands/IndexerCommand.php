<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.commands
 */
Yii::import('backend.components.*');
Yii::import('backend.components.db.*');
Yii::import('backend.components.interfaces.*');
Yii::import('backend.modules.product.models.*');
Yii::import('backend.models.behaviors.*');
Yii::import('frontend.share.behaviors.*');
Yii::import('frontend.share.helpers.*');
Yii::import('frontend.share.formatters.*');
Yii::import('frontend.share.validators.*');
Yii::import('frontend.extensions.upload.components.*');

/**
 * Class IndexerCommand
 *
 * Комана для индексирования паарметров фасеточного поиска
 */
class IndexerCommand extends CConsoleCommand
{
  const MAX_CHUNK_SIZE = 10000;

  /**
   * @var array
   */
  private $data = array();

  /**
   * @var CDbCommandBuilder
   */
  private $builder;

  /**
   * @var string
   */
  private $searchTable;

  /**
   * @var array
   */
  private $properties;

  /**
   * @var array
   */
  private $parameters;

  public function init()
  {
    parent::init();

    $this->builder = new CDbCommandBuilder(Yii::app()->db->getSchema());
    $this->searchTable = BFacetedSearch::model()->tableName();

    $this->properties = BFacetedParameter::model()->getProperties();
    $this->parameters = BFacetedParameter::model()->getParameters();
  }

  public function actionRefresh()
  {
    Yii::app()->db->beginTransaction();

    $this->actionDelete();
    $this->buildProperties();
    $this->buildParameters();
    $this->save();

    Yii::app()->db->currentTransaction->commit();
    $this->updateProduction();
  }

  public function actionDelete()
  {
    $this->builder->createSqlCommand("TRUNCATE TABLE ".$this->searchTable)->execute();
    $this->updateProduction();
  }

  private function updateProduction()
  {
    $path = Yii::getPathOfAlias('frontend.config.production').'.php';
    if( file_exists($path) )
      touch($path);
  }

  private function buildProperties()
  {
    $criteria = new CDbCriteria();
    $criteria->select = 't.id';
    $criteria->join = 'JOIN '.BProductAssignment::model()->tableName().' AS a ON a.product_id = t.id';

    foreach($this->properties as $property)
      $criteria->select .= ', '.Yii::app()->db->getSchema()->quoteColumnName($property);

    $command = $this->builder->createFindCommand(BProduct::model()->tableName(), $criteria);

    foreach($command->queryAll() as $row)
      foreach($this->properties as $property)
        if( !empty($row[$property]) )
          $this->data[] = array('product_id' => $row['id'], 'param_id' => $property, 'value' => $row[$property]);
  }

  private function buildParameters()
  {
    $criteria = new CDbCriteria();
    $criteria->distinct = true;
    $criteria->select = 't.param_id, t.product_id, t.variant_id, t.value, v.name';
    $criteria->join = 'LEFT OUTER JOIN '.BProductParamVariant::model()->tableName().' AS v ON v.id = t.variant_id';
    $criteria->addInCondition('t.param_id', $this->parameters);

    $command = $this->builder->createFindCommand(BProductParam::model()->tableName(), $criteria);

    foreach($command->queryAll() as $row)
      if( $value = !empty($row['variant_id']) ? $row['variant_id'] : $row['value'] )
        $this->data[] = array('product_id' => $row['product_id'], 'param_id' => $row['param_id'], 'value' => $value);
  }

  private function save()
  {
    if( !empty($this->data) )
    {
      $result = 0;

      foreach(array_chunk($this->data, self::MAX_CHUNK_SIZE) as $chunk)
      {
        $command = $this->builder->createMultipleInsertCommand($this->searchTable, $chunk);
        $count = $command->query()->count();
        $result += $count;
        echo 'Inserted '.$count.' record(s)'.PHP_EOL;
      }

      echo 'Total records: '.$result.'. Execution time: '.sprintf('%0.5f', Yii::getLogger()->getExecutionTime()).PHP_EOL;
      echo 'Memory peak usage: '.Yii::app()->format->formatSize(memory_get_peak_usage()).PHP_EOL;
    }
    else
    {
      throw new CException('Inserted 0 records', 500);
    }
  }
}