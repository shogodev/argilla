<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.xml
 */
class YandexPriceDataProvider extends YandexDataProvider
{
  protected function getUrl(Product $product)
  {
    return $product->url;
  }
}