<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 */

/**
 * Class ImportLogger
 */
class ImportLogger implements ILogger
{
  public $id;

  public $counter = 0;

  public $errorCounter = 0;

  /**
   * @var BProductImport
   */
  private $productImport;

  /**
   * @param string $message
   */
  public function log($message)
  {

  }

  /**
   * @param string $message
   *
   * @throws CException
   */
  public function error($message)
  {
    $this->createError($message);

    if( $this->errorCounter > 30 )
    {
      $this->createError('Слишком много ошибок. Импорт прекращен');
      die();
    }
  }

  /**
   * @param array $parameters
   */
  public function updateStatus(array $parameters)
  {
    $import = $this->getImport();

    if( isset($parameters['total_items']) )
    {
      $import->total_items = $parameters['total_items'];
    }

    if( isset($parameters['processed']) )
    {
      $import->process_item = ++$this->counter;
    }

    $import->last_update_time = date(DATE_ATOM, strtotime('now'));
    $import->used_memory = memory_get_usage();
    $import->save();
  }

  private function getImport()
  {
    if( $this->productImport === null )
    {
      $this->productImport = BProductImport::model()->findByPk($this->id);
    }

    return $this->productImport;
  }

  private function createError($message)
  {
    $model = new BProductImportMessage();
    $model->text = $message;
    $model->product_import_id = $this->id;
    $model->save(false);
  }
}