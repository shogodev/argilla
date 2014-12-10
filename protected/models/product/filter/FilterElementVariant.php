<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>, Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.product.filter
 *
 * @var string $notice
 */
class FilterElementVariant extends FilterElementItem
{
  public $notice;

  public function getImage()
  {
    $path = 'f/upload/images/'.$this->id.'.png';
    if( !file_exists($path) )
      $path = 'i/sp.gif';

    return $path;
  }
}