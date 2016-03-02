<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 */
abstract class BaseXml extends CComponent
{
  public $templatesAlias = 'frontend.views.xml';

  public $charset = 'windows-1251';

  public $template;

  public $filePath;

  public $dataProviderClass;

  /**
   * @var CDbCriteria
   */
  public $criteria;

  public $itemsBufferSize = 1000;

  /**
   * @var XMLWriter
   */
  protected $xmlWriter;

  protected $dataProvider;

  /**
   * @var bool $outCashedXml отдавать кэшированый файл
   */
  protected $outCashedXml = false;

  private $itemsBufferCounter = 0;

  abstract public function buildXml();

  public function init()
  {
    set_time_limit(0);

    $this->filePath = $this->getXmlPath();

    if( !$this->isInvalidateCache() )
    {
      $this->outCashedXml = true;

      return;
    }

    ignore_user_abort(true);
    $this->xmlWriter = new XMLWriter();
    $this->xmlWriter->openURI($this->filePath);

    if( isset($this->dataProviderClass) )
    {
      $this->criteria = isset($this->criteria) ? $this->criteria : new CDbCriteria();
      $this->dataProvider = new $this->dataProviderClass($this->criteria);
    }
  }

  public function render()
  {
    header('Content-Type: text/xml; charset='.$this->charset);

    readfile($this->filePath);

    Yii::app()->end();
  }

  protected function saveXml()
  {
    $dir = pathinfo($this->filePath, PATHINFO_DIRNAME);
    if( !file_exists($dir) )
    {
      mkdir($dir);
      chmod($dir, 0775);
    }

    $this->xmlWriter->flush();
    @chmod($this->filePath, 0775);
  }

  /**
   * @param string $template
   *
   * @return string $path
   * @throws InvalidArgumentException
   */
  protected function getTemplatePath($template)
  {
    if( $template === null )
    {
      $template = str_replace("Xml", "", lcfirst(get_called_class()));
    }

    $path = Yii::getPathOfAlias($this->templatesAlias.'.'.$template).'.php';

    if( !file_exists($path) )
    {
      throw new InvalidArgumentException('Отсутствует файл шаблона '.$path);
    }

    return $path;
  }

  /**
   * @return string $filePath
   */
  protected function getXmlPath()
  {
    if( !isset($this->filePath) )
    {
      $class = str_replace('DataProvider', '', $this->dataProviderClass);
      $this->filePath = 'f/xml/'.lcfirst($class).'.xml';
    }

    return $this->filePath;
  }

  /**
   * Следить за буфером, при необходимост и обнулить
   */
  protected function followBuffer()
  {
    if( $this->increaseBufferCounter() == $this->itemsBufferSize )
      $this->flushItemsBuffer();
  }

  private function flushItemsBuffer()
  {
    $this->xmlWriter->flush();
    $this->itemsBufferSize = 0;
  }

  private function increaseBufferCounter()
  {
    return $this->itemsBufferCounter++;
  }

  private function isInvalidateCache()
  {
    if( Yii::app()->request->getQuery('force') === 'force'  )
      return true;

    if( file_exists($this->filePath) )
    {
      if( !empty($this->cacheDurationInSeconds) && (time() - filemtime($this->filePath) > $this->cacheDurationInSeconds) )
        return true;

      return false;
    }

    return true;
  }
}