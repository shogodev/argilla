<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components
 */
class FProductsCollection
{
  protected $products = null;

  protected $collectionKey = 'key';

  protected $options = array();

  /**
   * @var CHttpSession
   */
  protected $session;

  public function __construct()
  {
    $this->session = Yii::app()->session;
  }

  public function dataForSend($productId, $valueOptions = array())
  {
    $data = array();

    $data['productId'] = $productId;

    foreach($this->options as $option => $defaultValue)
      $data[$option] = isset($valueOptions[$option]) ? $valueOptions[$option] : $defaultValue;

    return array($this->collectionKey => $data);
  }

  /**
   * @param $data
   * @return false|int индекс добавленного элемента
   */
  public function add($data)
  {
    $session_data = $this->getSessionData();

    $collectionIndex = $this->findElement($data, $session_data);
    if( $collectionIndex === false )
    {
      $session_data[] = $data;
      $this->setSessionData($session_data);
      return array_search($data, $session_data);
    }
    else
    {
      $count = isset($session_data[$collectionIndex]['count']) ? $session_data[$collectionIndex]['count'] : 1;
      $count += isset($data['count']) ? $data['count'] : 1;
      $session_data[$collectionIndex]['count'] = $count;

      $this->setSessionData($session_data);
      return $collectionIndex;
    }

    return false;
  }

  /**
   * @param $collectionIndex
   * @return bool
   */
  public function remove($collectionIndex)
  {
    $session_data = $this->getSessionData();

    if( isset($session_data[$collectionIndex]) )
    {
      unset($session_data[$collectionIndex]);
      $this->setSessionData($session_data);
      return true;
    }

    return false;
  }

  public function removeAll()
  {
    $this->session->remove($this->collectionKey);
    $this->resetProducts();
  }

  public function changeCount($collectionIndex, $count)
  {
    $session_data = $this->getSessionData();

    if( isset($session_data[$collectionIndex]) )
    {
      $session_data[$collectionIndex]['count'] = $count;
      $this->setSessionData($session_data);
      return true;
    }

    return false;
  }

  /**
   * @return Product[]|null
   */
  public function getProducts()
  {
    if( $this->products == null )
    {
      $ids  = array();
      $data = $this->getSessionData();

      foreach($data as $key => $value)
        $ids[$key] = $value['productId'];

      $products       = Product::model()->findAllByAttributes(array('id' => array_values($ids)));
      $productsBasket = array();

      foreach($data as $collectionIndex => $value)
      {
        $product = $this->findProduct($products, $value['productId']);

        $productsBasket[$collectionIndex]  = clone $product;

        foreach($product->basketParameters as $key => $basketParameters)
          $productsBasket[$collectionIndex]->basketParameters[$key] = clone $product->basketParameters[$key];

        $productsBasket[$collectionIndex]->collectionIndex = $collectionIndex;
        $productsBasket[$collectionIndex]->count           = isset($value['count']) ? $value['count'] : 1;
        $productsBasket[$collectionIndex]->sum             = $productsBasket[$collectionIndex]->price * $productsBasket[$collectionIndex]->count;

        foreach($this->options as $option => $defaultValue)
        {
          if( !empty($value[$option]) )
            $productsBasket[$collectionIndex]->basketParameters[$option]->value = $value[$option];
        }
      }

      $this->products = $productsBasket;
    }

    return $this->products;
  }

  /**
   * @param $collectionIndex
   * @return bool|BProduct
   */
  public function getProduct($collectionIndex)
  {
    $products = $this->getProducts();

    foreach($products as $product)
    {
      if( $product->collectionIndex == $collectionIndex )
        return $product;
    }

    return false;
  }

  public function getCollectionKey()
  {
    return $this->collectionKey;
  }

  public function getProductsIds()
  {
    $ids  = array();
    $data = $this->getSessionData();

    foreach($data as $key => $value)
      $ids[$key] = $value['productId'];

    return $ids;
  }

  protected function findElement($data, $session_data)
  {
    $exception_keys = array('count');

    // todo: в дальнейшем можно сделать проверку совпадения елементов по нужным параметрам
    foreach($session_data as $key => $value)
    {
      if( $this->compare($value, $data, $exception_keys) )
        return $key;
     }

     return false;
  }

  /**
   * @param $products
   * @param $productId
   * @return BProduct|null
   */
  private function findProduct($products, $productId)
  {
    foreach($products as $product)
    {
      if( $product->id == $productId )
        return $product;
    }

    return null;
  }

  private function compare($element1, $element2, $exception_keys = array())
  {
    foreach($exception_keys as $key)
      unset($element1[$key], $element2[$key]);

    ksort($element1);
    ksort($element2);

    if( $element1 == $element2 )
      return true;

    return false;
  }

  protected function getSessionData()
  {
    return isset($this->session[$this->collectionKey]) ? $this->session[$this->collectionKey] : array();
  }

  private function setSessionData($session_data)
  {
    $this->session[$this->collectionKey] = $session_data;
    $this->resetProducts();
  }

  private function resetProducts()
  {
    $this->products = null;
  }
}