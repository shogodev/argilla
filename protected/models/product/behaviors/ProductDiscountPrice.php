<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 */
class ProductDiscountPrice extends CBehavior
{
  /**
   * @return string
   */
  public function getPrice()
  {
    return PriceHelper::isEmpty($this->owner->getAttribute('price_old')) ? $this->owner->getAttribute('price') - PriceHelper::getPercent($this->owner->getAttribute('price'), $this->getDiscountPercent()) : $this->owner->getAttribute('price');
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
}