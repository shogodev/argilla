<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.xml
 */
class YandexMarketXml extends AbstractXml
{
  public $dataProviderClass = 'YandexDataProvider';

  /**
   * @var CDbCriteria
   */
  public $criteria;

  /**
   * @var YandexDataProvider
   */
  private $dataProvider;

  public function init()
  {
    parent::init();

    if( !isset($this->criteria) )
    {
      $this->criteria = new CDbCriteria();
    }

    $this->xmlDocument->addAttribute('date', date("Y-m-d H:i"));
    $this->dataProvider = new $this->dataProviderClass($this->criteria);

    $this->setShop();
    $this->setCurrencies();
    $this->setCategories();
    $this->setOffers();
  }

  private function setShop()
  {
    $shop = $this->xmlDocument->xpath('//shop')[0];

    foreach($this->dataProvider->getShop() as $key => $value)
    {
      $shop->addChild($key, XmlHelper::escape($value));
    }
  }

  private function setCurrencies()
  {
    $currencies = $this->xmlDocument->xpath('//shop')[0]->addChild('currencies');

    foreach($this->dataProvider->getCurrencies() as $key => $value)
    {
      $currency = $currencies->addChild('currency');
      $currency->addAttribute('id', $key);
      $currency->addAttribute('rate', $value);
    }
  }

  private function setCategories()
  {
    $categories = $this->xmlDocument->xpath('//shop')[0]->addChild('categories');

    foreach($this->dataProvider->getCategories() as $key => $value)
    {
      $category = $categories->addChild('category', $value['name']);
      $category->addAttribute('id', $key);

      if( isset($value['parent']) )
      {
        $category->addAttribute('parentId', $value['parent']);
      }
    }
  }

  private function setOffers()
  {
    $offers = $this->xmlDocument->xpath('//shop')[0]->addChild('offers');

    foreach($this->dataProvider->getOffers() as $value)
    {
      $offer = $offers->addChild('offer');
      $offer->addAttribute('id', $value['id']);
      $offer->addAttribute('available', !empty($value['available']) ? 'true' : 'false');
      $offer->addAttribute('type', 'vendor.model');

      foreach(array('url', 'price', 'currencyId', 'categoryId', 'picture', 'typePrefix', 'vendor', 'vendorCode', 'model', 'description', 'manufacturer_warranty') as $child)
      {
        if( !empty($value[$child]))
        {
          $offer->addChild($child, $value[$child]);
        }
      }
    }
  }
}