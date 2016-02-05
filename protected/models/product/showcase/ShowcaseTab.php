<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
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
  private $customData;

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
    return Utils::buildUrl(array(
      'path' => Yii::app()->request->hostInfo.Yii::app()->request->requestUri, //используем сырой url потому что, getCurrentAbsoluteUrl работает некоррекно с get параметрами вида "?name"
      'fragment' => $this->getPrefix()
    ));
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

  public function setCustomData($customData)
  {
    $this->customData = $customData;
  }

  public function getCustomData()
  {
    return $this->customData;
  }

  public function __sleep()
  {
    unset($this->dataProvider);
    return array_keys((array)$this);
  }
}