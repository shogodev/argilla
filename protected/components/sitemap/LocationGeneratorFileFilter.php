<?php
/**
 * @author Vladimir Utenkov <utenkov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components.sitemap
 */
class LocationGeneratorFileFilter extends FilterIterator
{
  /**
   * @return bool
   */
  public function accept()
  {
    return strpos($this->current(), 'Location') !== false;
  }
}