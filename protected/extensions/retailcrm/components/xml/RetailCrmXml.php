<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.xml
 */
/**
 * Class RetailCrmXml
 * @property RetailCrmDataProvider $dataProvider
 */
class RetailCrmXml extends BaseXml
{
  public $dataProvider;

  private $offerCounter = 0;

  private $debugLimit = null;

  public function buildXml()
  {
    $this->xmlWriter->setIndent(true);
    $this->xmlWriter->startDocument('1.0', $this->charset);
    $this->xmlWriter->text('<!DOCTYPE yml_catalog SYSTEM "shops.dtd">'.PHP_EOL);
    $this->xmlWriter->startElement('yml_catalog');
    $this->xmlWriter->writeAttribute('date', date("Y-m-d H:i"));
    $this->xmlWriter->startElement('shop');

    $this->setShop();
    $this->setCategories();
    $this->setOffers();

    $this->xmlWriter->endElement();
    $this->xmlWriter->endElement();

    $this->saveXml();
  }

  public function setCategory($attributes = array())
  {
    $this->xmlWriter->startElement('category');
    $this->xmlWriter->writeAttribute('id', $attributes['id']);

    if( isset($attributes['parent']) )
    {
      $this->xmlWriter->writeAttribute('parentId', $attributes['parent']);
    }

    $this->xmlWriter->text($attributes['name']);

    $this->xmlWriter->endElement();
  }

  protected function setShop()
  {
    foreach($this->dataProvider->getShop() as $key => $value)
    {
      $this->xmlWriter->writeElement($key, XmlHelper::escape($value));
    }
  }

  protected function setCategories()
  {
    $this->xmlWriter->startElement('categories');

    foreach($this->dataProvider->getCategories() as $attributes)
    {
      $this->setCategory($attributes);
    }

    $this->xmlWriter->endElement();
  }

  protected function setOffers()
  {
    $st = 0;

    $this->xmlWriter->startElement('offers');
    foreach($this->dataProvider->getOffers() as $offersList)
    {
      foreach($offersList as $attributes)
      {
        $this->setOffer($attributes);
        $this->followBuffer();

        Yii::app()->retailCrm->createDebugReport($attributes, $this->offerCounter++);
        Yii::app()->retailCrm->increaseExportProductCounter();

        if( !empty($this->debugLimit) )
        {
          if( $st++ == $this->debugLimit )
            break 2;
        }
      }
    }
    $this->xmlWriter->endElement();
  }

  protected function setOffer(array $offer)
  {
    $this->xmlWriter->startElement('offer');
    $this->xmlWriter->writeAttribute('id', $offer['id']);
    $this->xmlWriter->writeAttribute('productId', $offer['productId']);

    if( isset($offer['quantity']) )
      $this->xmlWriter->writeAttribute('quantity', $offer['quantity']);

    foreach(Arr::extract($offer, array('url', 'price', 'purchasePrice', 'categories', 'picture', 'name', 'xmlId', 'productName', 'vendor'))  as $attribute => $value)
    {
      if( empty($value) )
        continue;

      if( $attribute == 'categories' )
      {
        foreach($value as $categoryId)
        {
          $this->xmlWriter->writeElement('categoryId', XmlHelper::escape($categoryId));
        }
      }
      else
      {
        $this->xmlWriter->writeElement($attribute, $value);
      }
    }

    $this->setParameters($offer);

    $this->xmlWriter->endElement();
  }

  protected function setParameters(array $offer)
  {
    if( empty($offer['params']) )
      return;

    foreach($offer['params'] as $param)
    {
      $this->xmlWriter->startElement('param');
      $this->xmlWriter->writeAttribute('name', XmlHelper::escape($param['name']));

      if( !empty($param['code']) )
        $this->xmlWriter->writeAttribute('code', XmlHelper::escape($param['code']));

      $this->xmlWriter->text(XmlHelper::escape($param['value']));

      $this->xmlWriter->endElement();
    }
  }
}