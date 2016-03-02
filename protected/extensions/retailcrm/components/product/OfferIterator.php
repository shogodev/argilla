<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 */
class OfferIterator extends CDataProviderIterator
{
  /**
   * @var RetailCrmProductList $productList
   */
  public $productList;

  public $buildCategoryCallback;

  public function current()
  {
    /**
     * @var Product $product
     */
    $product = parent::current();

    $offers = array();

    if( $basketParameter = $product->getBasketParameter(true) )
    {
      foreach($basketParameter->getParameters() as $parameter)
      {
        $offers[] = $this->buildOfferByParameter($product, $parameter);
      }
    }
    else
    {
      $offers = array($this->getOffer($product));
    }

    $product->detachBehaviors();

    return $offers;
  }

  protected function getOffer(Product $product)
  {
    $offer = $this->buildOffer($product);

    $product->detachBehaviors();

    return $offer;
  }

  protected function buildOffer(Product $product)
  {
    $parent = $product;

    $offer = array(
      'id' => $product->id,
      'xmlId' => $product->id,
      'productId' => $parent->id,
      'price' => round($product->getPrice()),
      'url' => $this->getUrl($product),
      'categories' => $this->buildCategory($parent),
      'name' => XmlHelper::escape($product->getHeader()),
      'productName' =>XmlHelper::escape($product->getHeader()),
      'picture' => $product->image ? Yii::app()->request->hostInfo.$product->image : '',
      'vendor' => isset($parent->category) ? $parent->category->name : '',
      'quantity' => $product->dump == 1 ? 1000 : 0,
    );

    $offer['params'] = $this->getParameters($product, $product->getParameters());

    return $offer;
  }

  protected function buildOfferByParameter(Product $product, ProductParameter $parameter)
  {
    $id = $product->id.'_'.$parameter->id;
    $offer = array(
      'id' => $id,
      'xmlId' => $id,
      'productId' => $product->id,
      'price' => round(PriceHelper::isNotEmpty($parameter->price) ? $parameter->price : $product->price),
      'url' => $this->getUrl($product),
      'categories' => $this->buildCategory($product),
      'name' => XmlHelper::escape($product->getHeader()." (".$parameter->parameterName->name.": ".$parameter->variant->name.")"),
      'productName' =>XmlHelper::escape($product->getHeader()),
      'picture' => $product->image ? Yii::app()->request->hostInfo.$product->image : '',
      'vendor' => isset($product->category) ? $product->category->name : '',
      'quantity' => $product->dump == 1 ? 1000 : 0,
    );

    $offer['params'] = $this->getParameters($product, $product->getProductOneParameters());

    return $offer;
  }

  /**
   * @param Product $product
   * @param ProductParameterName[] $productParameters
   *
   * @return array
   */
  protected function getParameters(Product $product, $productParameters)
  {
    $parameters = array();

    if( !empty($product->articul) )
    {
      $parameters['article'] = array(
        'code' => 'article',
        'name' => 'Ариткул',
        'value' => $product->articul
      );
    }

    if( !empty($product->offer_id) )
    {
      $parameters['offer_id'] = array(
        'code' => 'offer_id',
        'name' => 'OfferId',
        'value' => $product->offer_id
      );
    }

    foreach($productParameters as $parameter)
    {
      $code = 'param_id_'.$parameter->id;
      $parameters[$code] = array(
        'code' => $code,
        'name' => $parameter->name,
        'value' => $parameter->value
      );
    }

    return $parameters;
  }

  public function getPictureRetailCrm($product, $pictures)
  {
    $picture = Arr::get($pictures, $product->id);

    if( !$picture )
    {
      if( $product->parent > 0 )
        $picture = Arr::get($pictures, $product->parent);
    }

    return $picture;
  }

  protected function buildCategory(Product $product)
  {
    $categoryIds = call_user_func($this->buildCategoryCallback, $product);

    return $categoryIds;
  }

  /**
   * @param Product $product
   *
   * @return bool
   */
  protected function getUrl(Product $product)
  {
    return $product->getUrl(true);
  }
}