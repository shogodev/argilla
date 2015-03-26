<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 *
 * @property Product $owner
 */
class ProductUnitPriceBehavior extends CBehavior
{
  /**
   * @return string
   */
  public function getPriceRaw()
  {
    return $this->owner->getAttribute('price');
  }

  /**
   * @return string
   */
  public function getPriceOldRaw()
  {
    return $this->owner->getAttribute('price_old');
  }

  /**
   * @return string
   */
  public function getPrice()
  {
    return intval($this->isUnit() ? ($this->getPriceRaw() * $this->owner->area) : $this->getPriceRaw());
  }

  /**
   * @param string $prefix
   *
   * @return string
   */
  public function getUnit($prefix = '')
  {
    return $this->isUnit() ? $prefix.'м<sup>2</sup>' : '';
  }

  public function getArea()
  {
    return str_replace('.', ',', $this->owner->area);
  }

  /**
   * @return string
   */
  public function getPriceOld()
  {
    return intval($this->isUnit() ? ($this->getPriceOldRaw() * $this->owner->area) : $this->getPriceOldRaw());
  }

  /**
   * @return bool
   */
  public function isUnit()
  {
    return !PriceHelper::isEmpty($this->owner->area);
  }
}