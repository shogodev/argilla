<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 *
 * public function behaviors()
 * {
 *   return array(
 *     'productDumpBehavior' => array('class' => 'ProductDumpBehavior'),
 *   );
 *  }
 */


/**
 * Class ProductDumpBehavior
 *
 * @property Product $owner
 */
class ProductDumpBehavior extends SActiveRecordBehavior
{
  public function getDumpName()
  {
    return Utils::ucfirst(ProductDump::getName($this->owner->dump));
  }

  public function getDumpFullName()
  {
    return $this->getDumpName().' '.$this->getDumpDescription();
  }

  public function getDumpDescription()
  {
    return ProductDump::getDescription($this->owner->dump);
  }

  public function getDumpClass()
  {
    switch($this->owner->dump)
    {
      case ProductDump::NOT_AVAILABLE:
        return 'product-avail not-avail';
      break;

      case ProductDump::AVAILABLE:
        return 'product-avail';
      break;

      case ProductDump::AVAILABLE_ORDER:
        return 'product-avail avail-order';
        break;
    }
  }
}