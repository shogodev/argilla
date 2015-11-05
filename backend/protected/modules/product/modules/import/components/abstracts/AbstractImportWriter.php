<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 */
abstract class AbstractImportWriter
{
  /**
   * @var ConsoleFileLogger
   */
  public $logger;

  private $useCurrentTransaction;

  public function __construct(ConsoleFileLogger $logger)
  {
    $this->logger = $logger;
  }

  abstract public function write(array $data);

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
}