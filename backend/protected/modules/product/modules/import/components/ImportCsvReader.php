<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 */
Yii::import('frontend.share.formatters.SFormatter');

class ImportCsvReader
{
  /**
   * @var int $headerRowIndex - индекс заголовка (нуменация начинается с 1)
   */
  public $headerRowIndex = 1;

  /**
   * @var int $skipTopRowAmount - количество попушенних строк с начала файла
   */
  public $skipTopRowAmount = 1;

  public $csvDelimiter = ',';

  protected $header;

  protected $currentFileName;

  protected $currentRow;

  /**
   * @var ConsoleFileLogger
   */
  private $logger;

  /**
   * @var AbstractAggregator
   */
  private $importAggregator;

  private $timeBegin;

  private $currentFile;

  /**
   * @var FileBackup $fileBackup
   */
  private $fileBackup;

  public function __construct(ConsoleFileLogger $logger, AbstractAggregator $importAggregator)
  {
    $this->logger = $logger;
    $this->importAggregator = $importAggregator;
    $this->basePath = realpath(Yii::getPathOfAlias('frontend').'/..');
  }

  public function start()
  {
    $this->timeBegin = microtime(true);
    $this->logger->log('Начало импорта');
  }

  public function processFiles($files = array())
  {
    $counter = 1;
    foreach($files as $file)
    {
      $this->logger->log('Обработка файла '.$file.' '.($counter++).'/'.count($files));

      try
      {
        $this->processFile($file);
      }
      catch(WarningException $e)
      {
        $this->logger->warning($e->getMessage());
      }
    }
  }

  public function finish()
  {
    $time = microtime(true) - $this->timeBegin;
    $this->logger->log('Импорт завершен. Время выполнения '.sprintf("%.1f", $time).' с.');
  }

  public function setBackupConfig($newPath)
  {
    $this->fileBackup = new FileBackup(ImportHelper::wrapInSlashEnd($newPath));
  }

  protected function processFile($file)
  {
    if( !($handle = @fopen($file, 'r')) )
      throw new WarningException('Не удальсь открыть файл '.$file);

    $this->currentFile = $file;

    $this->currentFileName = basename($file);
    $progress = new ConsoleProgressBar($this->countFileLines($file));
    $this->processData($handle, $progress);
  }

  protected function processData($handle, ConsoleProgressBar $progress)
  {
    $this->currentRow = 0;

    $this->importAggregator->init();
    $progress->start();
    while(($item = fgetcsv($handle, null, $this->csvDelimiter)) !== false)
    {
      $this->currentRow++;
      $progress->setValueMap('memory', Yii::app()->format->formatSize(memory_get_usage()));
      $progress->advance();

      if( !is_null($this->headerRowIndex) && $this->currentRow == $this->headerRowIndex )
      {
        $this->header = $this->formatItem($item);
        $this->importAggregator->setHeader($this->header);
      }

      if( $this->currentRow <= $this->skipTopRowAmount )
        continue;

      $this->importAggregator->collect($this->formatItem($item), $this->currentRow, $this->currentFileName);
    }
    $progress->finish();

    $this->onEndReadFile($handle);

    $this->importAggregator->end();
  }

  protected function formatItem($item)
  {
    foreach($item as $key => $value)
    {
      $item[$key] = trim($value, '"\' ');
    }

    return $item;
  }

  protected function countFileLines($file)
  {
    //todo: Сделать альтернативу для windows

    $file = realpath($this->basePath.'/'.$file);
    $result = trim(exec("wc -l '$file'"));

    return intval(substr($result, 0, strpos($result, ' ')));
  }

  protected function onEndReadFile($handle)
  {
    if( $handle )
      fclose($handle);

    if( $this->fileBackup )
      $this->fileBackup->makeBackup($this->currentFile);
  }
}