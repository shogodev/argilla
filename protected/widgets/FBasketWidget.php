<?php
class FBasketWidget extends CWidget
{
  /**
   * @var FBasket
   */
  public $basket;

  public $count = 0;
  public $sum   = 0;
  public $url   = '';
  public $collectionKey   = '';

  public function init()
  {
    $this->count         = $this->basket->getCount();
    $this->sum           = $this->basket->getSum();
    $this->url           = $this->basket->getBasketUrl();
    $this->collectionKey = $this->basket->getCollectionKey();
  }

  public function run()
  {
    $this->render('basketWidget');
  }
}