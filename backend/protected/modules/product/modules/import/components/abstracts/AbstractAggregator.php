<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 */
abstract class AbstractAggregator
{
  /**
   * @var array
   */
  protected $data;

  /**
   * @var AbstractImportWriter
   */
  protected $writer;

  protected $header;

  public function __construct(AbstractImportWriter $writer)
  {
    $this->writer = $writer;
  }

  abstract public function process($data, $rowIndex, $file);

  public function init()
  {

  }

  public function collect($data, $rowIndex, $file)
  {
    $this->process($data, $rowIndex, $file);
  }

  public function end()
  {
    $this->writer->write($this->data);
  }

  public function setHeader($header)
  {
    $this->header = $header;
  }

  protected function convertColumnIndexes(&$array)
  {
    foreach($array as $key => $columnIndex)
    {
      if( !empty($columnIndex) && !is_numeric($columnIndex) )
        $array[$key] = ImportHelper::lettersToNumber($columnIndex);
      else
        unset($array[$key]);
    }
  }
}