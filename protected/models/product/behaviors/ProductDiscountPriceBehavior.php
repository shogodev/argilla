<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 */
class ProductDiscountPriceBehavior extends CBehavior
{
  /**
   * @return string
   */
  public function getPrice()
  {
    if( PriceHelper::isEmpty($this->owner->getAttribute('price_old')) )
    {
      return $this->owner->getAttribute('price') - PriceHelper::getPercent($this->owner->getAttribute('price'), $this->getDiscountPercent());;
    }

    $oldPriceWithDiscount = $this->owner->getAttribute('price_old') - PriceHelper::getPercent($this->owner->getAttribute('price_old'), $this->getDiscountPercent());

    return min(floatval($this->owner->getAttribute('price')), floatval($oldPriceWithDiscount) );
  }

  /**
   * @return string
   */
  public function getPriceOld()
  {
    return PriceHelper::isNotEmpty($this->owner->getAttribute('price_old')) || PriceHelper::isEmpty($this->getDiscountPercent()) ? $this->owner->getAttribute('price_old') : $this->owner->getAttribute('price');
  }

  /**
   * @return string
   */
  public function getDiscountPercent()
  {
    return Yii::app()->user->profile->discount;
  }

  /**
   * @return string
   */
  public function getRealDiscountPrice()
  {
    $discountPrice = PriceHelper::getPercent(floatval($this->owner->getAttribute('price')), $this->getDiscountPercent());

    if( PriceHelper::isEmpty($this->owner->getAttribute('price_old')) )
    {
      return $discountPrice;
    }
    else
    {
      $oldPriceDiscount = PriceHelper::getPercent(floatval($this->owner->getAttribute('price_old')), $this->getDiscountPercent());

      if( (floatval($this->owner->getAttribute('price')) - $discountPrice) < (floatval($this->owner->getAttribute('price_old')) - $oldPriceDiscount) )
        return $discountPrice;
      else
        return $oldPriceDiscount;
    }
  }
}