<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 */
abstract class AbstractAggregator
{
  public $groupByColumn;

  /**
   * @var integer|null $collectItemBufferSize чилсо строк обработанных перет записью, если null то сначало все соберается, потом пишется
   */
  public $collectItemBufferSize;

  /**
   * @var array
   */
  protected $data;

  /**
   * @var AbstractImportWriter
   */
  protected $writer;

  protected $header;

  private $lastGroupIndex;

  private $innerIndexCounter;

  private $itemBufferCounter;

  public function __construct(AbstractImportWriter $writer)
  {
    $this->writer = $writer;
  }

  abstract public function process($data, $rowIndex, $file, $groupIndex);

  public function init()
  {
    $this->clearData();
    $this->writer->init();
  }

  public function collect($data, $rowIndex, $file)
  {
    $groupIndex = !empty($this->groupByColumn) || $this->groupByColumn === 0 ? $data[$this->groupByColumn] : $this->innerIndexCounter++;

    $this->itemBufferCounter++;

    if( $this->collectItemBufferSize )
    {
      if( $this->lastGroupIndex != $groupIndex && $this->itemBufferCounter >= $this->collectItemBufferSize )
      {
        $this->writer->writePartial($this->data);
        $this->clearData();
        $this->itemBufferCounter = 0;
      }

      if( !empty($this->groupByColumn) )
      {
        $this->lastGroupIndex = $groupIndex;
      }
    }

    $this->process($data, $rowIndex, $file, $groupIndex);
  }

  public function end()
  {
    if( is_null($this->collectItemBufferSize) )
    {
      $this->writer->writeAll($this->data);
    }
    else
    {
      $this->writer->writePartial($this->data);
    }

    $this->clearData();
    $this->writer->showStatistics();
  }

  public function setHeader($header)
  {
    $this->header = $header;
  }

  protected function clearData()
  {
    $this->data = array();
  }
}