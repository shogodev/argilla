<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2016 Shogo
 * @license http://argilla.ru/LICENSE
 */
class CurrentTransactionBehavior extends CBehavior
{
  protected $useCurrentTransaction;

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