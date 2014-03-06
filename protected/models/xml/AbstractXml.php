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

  /**
   * @var SimpleXMLElement
   */
  protected $xmlDocument;

  public function init()
  {
    $this->xmlDocument = new SimpleXMLElement($this->getTemplatePath($this->template), false, true);
  }

  public function render()
  {
    header('Content-Type: text/xml; charset='.$this->charset);
    echo $this->xmlDocument->asXML();
    Yii::app()->end();
  }

  /**
   * @param mixed $string
   *
   * @return mixed
   */
  protected function escape($string)
  {
    if( is_array($string) )
    {
      foreach($string as $key => $value)
      {
        $string[$key] = $this->escape($value);
      }
    }

    return htmlspecialchars(trim($string));
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
}