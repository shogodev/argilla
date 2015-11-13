<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 */
/**
 * Class ProductParameterPriceBehavior - Поведение для товаров с ценами зависящими от параметра корзины
 * @property Product $owner
 */
class ProductParameterPriceBehavior extends CBehavior
{
  public $basketParameterKey = 'parameter';

  /**
   * @return string
   */
  public function getPrice()
  {
    if( $parameterName = $this->owner->getBasketParameter() )
    {
      if( $this->owner->getCollectionItems($this->basketParameterKey) )
        return $this->owner->getCollectionItemSum($this->basketParameterKey);

      return $parameterName->getParameter()->price;
    }

    return $this->owner->getAttribute('price');
  }

  /**
   * @return string
   */
  public function getPriceOld()
  {
    return $this->owner->getAttribute('price_old');
  }
}