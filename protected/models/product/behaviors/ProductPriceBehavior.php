<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 *
 * @property Product $owner
 */
class ProductPriceBehavior extends CBehavior
{
  /**
   * @return string
   */
  public function getPrice()
  {
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