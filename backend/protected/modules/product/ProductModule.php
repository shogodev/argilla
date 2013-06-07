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

  /**
   * Задаем параметры наложения ватермарка на изображения
   *
   * @return array
   *
   * Example:
   *
   * return array(
   *  'product' => array(
   *    'pre' => array(
   *      'image' => 'watermarks/pre.png',
   *      'position' => 'centerBottom',
   *      'offsetX' => 0,
   *      'offsetY' => -20,
   *      ),
   *    ),
   *  );
   */
  public function getWatermarkSettings()
  {
    return array();
  }
}