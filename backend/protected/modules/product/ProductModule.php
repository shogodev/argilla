<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.product
 */
class ProductModule extends BModule
{
  public $defaultController = 'BProduct';
  public $name = 'Каталог продукции';

  public function getThumbsSettings()
  {
    return array(
      'product' => array(
        'origin' => array(4500, 4500),
        'pre' => array(130, 70),
      )
    );
  }
}