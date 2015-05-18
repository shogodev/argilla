<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.xml
 */
abstract class AbstractXml extends CComponent
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

  public $cacheDurationInSeconds = 86400;

  /**
   * @var SimpleXMLElement
   */
  protected $xmlDocument;

  protected $dataProvider;

  abstract public function buildXml();

  public function init()
  {
    $this->filePath = $this->getXmlPath();

    if( !$this->loadXml() )
    {
      $this->xmlDocument = new SimpleXMLElement($this->getTemplatePath($this->template), false, true);

      if( isset($this->dataProviderClass) )
      {
        $this->criteria = isset($this->criteria) ? $this->criteria : new CDbCriteria();
        $this->dataProvider = new $this->dataProviderClass($this->criteria);
      }

      $this->buildXml();
      $this->saveXml();
    }
  }

  public function render()
  {
    header('Content-Type: text/xml; charset='.$this->charset);
    echo $this->xmlDocument->asXML();
    Yii::app()->end();
  }

  /**
   * @return bool
   */
  private function loadXml()
  {
    if( Yii::app()->request->getQuery('force') === 'force' || !file_exists($this->filePath) || ( time() - filemtime($this->filePath) > $this->cacheDurationInSeconds ) )
    {
      set_time_limit(0);
      ignore_user_abort(true);
      return false;
    }
    else
    {
      $this->xmlDocument = simplexml_load_file($this->filePath);
      return true;
    }

    return false;
  }

  private function saveXml()
  {
    $dir = pathinfo($this->filePath, PATHINFO_DIRNAME);
    if( !file_exists($dir) )
    {
      mkdir($dir);
      chmod($dir, 0775);
    }

    $this->xmlDocument->saveXML($this->filePath);
    chmod($this->filePath, 0775);
  }

  /**
   * @param string $template
   *
   * @return string $path
   * @throws InvalidArgumentException
   */
  private function getTemplatePath($template)
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
  private function getXmlPath()
  {
    if( !isset($this->filePath) )
    {
      $class = str_replace('DataProvider', '', $this->dataProviderClass);
      $this->filePath = 'f/xml/'.lcfirst($class).'.xml';
    }

    return $this->filePath;
  }
}