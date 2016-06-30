<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 */

/**
 * Class AbstractImportCommand
 * @mixin CurrentTransactionBehavior
 */
class AbstractImportCommand extends LoggingCommand
{
  public $logFileName = 'import.log';

  protected $useCurrentTransaction;

  public function behaviors()
  {
    return CMap::mergeArray(
      parent::behaviors(),
      array(
        'currentTransactionBehavior' => array('class' => 'fronted.share.behaviors.CurrentTransactionBehavior')
      )
    );
  }

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