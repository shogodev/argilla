<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.modules.product
 *
 * @property string $name
 * @property string $index
 * @property string $url
 * @property FActiveRecord[] $data
 * @property FArrayDataProvider $dataProvider
 */
class Tab extends CComponent
{
  public $tabPrefix = 'tab-';

  protected $name;

  protected $dataProvider;

  protected $index;

  public function __construct($name, FActiveDataProvider $dataProvider, $index)
  {
    $this->name = $name;
    $this->index = $index;
    $this->dataProvider = $dataProvider;
  }

  public function getName()
  {
    return $this->name;
  }

  public function getUrl()
  {
    return Utils::buildUrl(array(
      'path' => Yii::app()->controller->getCurrentAbsoluteUrl(),
      'fragment' => $this->getIndex()
    ));
  }

  public function getIndex()
  {
    return $this->tabPrefix.$this->index;
  }

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