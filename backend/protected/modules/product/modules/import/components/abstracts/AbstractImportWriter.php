<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 */
abstract class AbstractImportWriter extends CComponent
{
  /**
   * @var ConsoleFileLogger
   */
  public $logger;

  public $clear = false;

  public $clearTables = array();

  public $uniqueAttribute = 'articul';

  private $useCurrentTransaction;

  public function __construct(ConsoleFileLogger $logger)
  {
    $this->logger = $logger;
  }

  public function init()
  {
    $this->clear();
  }

  public function clear()
  {
    if( $this->clear && !empty($this->clearTables) )
    {
      $this->logger->log('Очистка БД');
      $this->clearTables();
      $this->clear = false;
    }
  }

  abstract public function writeAll(array $data);

  abstract public function writePartial(array $data);

  abstract public function showStatistics();

  public function beginTransaction()
  {
    if( Yii::app()->db->getCurrentTransaction() && is_null($this->useCurrentTransaction) )
    {
      $this->useCurrentTransaction = false;
      return;
    }

    if( is_null($this->useCurrentTransaction) )
    {
      Yii::app()->db->beginTransaction();
      $this->useCurrentTransaction = true;
    }
  }

  public function commitTransaction()
  {
    if( $this->useCurrentTransaction )
    {
      Yii::app()->db->currentTransaction->commit();
      $this->useCurrentTransaction = null;
    }
  }

  public function rollbackTransaction()
  {
    if( $this->useCurrentTransaction )
    {
      Yii::app()->db->currentTransaction->rollback();
      $this->useCurrentTransaction = null;
    }
  }

  protected function clearTables()
  {
    foreach($this->clearTables as $table)
    {
      if( strpos($table, 'product_param_name') !== false )
      {
        $this->clearParameterNames();
      }
      else
      {
        $command = Yii::app()->db->createCommand(Yii::app()->db->schema->truncateTable($table));
        if( $command->execute() )
          throw new WarningException("Не удаллсь очистить таблицу ".$table);
      }

      $this->logger->log('Таблица '.$table.' очищена');
    }
  }

  protected function clearParameterNames()
  {
    $builder = new CDbCommandBuilder(Yii::app()->db->getSchema());
    $builder->createSqlCommand("DELETE FROM `{{product_param_name}}` WHERE parent > 1")->execute();
    $builder->createSqlCommand("DELETE FROM `{{product_param_name}}` WHERE id > 2")->execute();
    $builder->createSqlCommand("ALTER TABLE `{{product_param_name}}` AUTO_INCREMENT = 3")->execute();
  }
}