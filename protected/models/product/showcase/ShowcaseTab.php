<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.modules.product
 *
 * @property string $name
 * @property string $index
 * @property string $prefix
 * @property string $url
 * @property FActiveRecord[] $data
 * @property FArrayDataProvider $dataProvider
 */
class ShowcaseTab extends CComponent
{
  /**
   * @var string
   */
  public $tabPrefix = 'tab-';

  /**
   * @var string
   */
  private $name;

  /**
   * @var FActiveDataProvider
   */
  private $dataProvider;

  /**
   * @var integer
   */
  private $index;

  /**
   * @var string
   */
  private $url;

  /**
   * @param string $name
   * @param FActiveDataProvider $dataProvider
   * @param integer $index
   */
  public function __construct($name, FActiveDataProvider $dataProvider, $index)
  {
    $this->name = $name;
    $this->index = $index;
    $this->dataProvider = $dataProvider;
  }

  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * @return string
   */
  public function getUrl()
  {
    if( !isset($this->url) )
    {
      $this->url = Utils::buildUrl(array(
        'path' => Yii::app()->controller->getCurrentAbsoluteUrl(),
        'fragment' => $this->getPrefix()
      ));
    }

    return $this->url;
  }

  /**
   * @return string
   */
  public function getIndex()
  {
    return $this->index + 1;
  }

  public function getPrefix()
  {
    return $this->tabPrefix.$this->index;
  }

  /**
   * @return array
   */
  public function getData()
  {
    return $this->getDataProvider()->getData();
  }

  /**
   * @return FActiveDataProvider
   */
  public function getDataProvider()
  {
    return $this->dataProvider;
  }

  /**
   * @return FArrayDataProvider
   */
  public function getRandomDataProvider()
  {
    return new FArrayDataProvider($this->getDataProvider()->getDataRandom(), array('pagination' => false));
  }
}