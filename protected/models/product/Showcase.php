<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.product
 */
class Showcase extends SplObjectStorage
{
  public function __construct($limit = null)
  {
    $this->addSpec('Акции', $limit);
    $this->addNovelty('Новинки', $limit);
  }

  protected function addSpec($name, $limit)
  {
    $criteria = new CDbCriteria();
    $criteria->compare('t.main', 1);
    $criteria->compare('t.spec', 1);
    $criteria->compare('t.visible', 1);

    if( $limit )
      $criteria->limit = $limit;

    $this->createTab($name, $criteria);
  }

  protected function addNovelty($name, $limit)
  {
    $criteria = new CDbCriteria();
    $criteria->compare('t.main', 1);
    $criteria->compare('t.novelty', 1);
    $criteria->compare('t.visible', 1);

    if( $limit )
      $criteria->limit = $limit;

    $this->createTab($name, $criteria);
  }

  protected function createTab($name, $criteria)
  {
    $productList = new ProductList($criteria, null, false);

    $dataProvider = $productList->getRandomDataProvider();
    if( !$dataProvider || !$dataProvider->totalItemCount )
      return;

    $tab = new Tab($name, $dataProvider, $this->count());
    $this->attach($tab);
  }
}