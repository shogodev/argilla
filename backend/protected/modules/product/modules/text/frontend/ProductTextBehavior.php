<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 *
 * Пример подключения:
 * <pre>
 *   ...
 *   'productTextBehavior' => array('class' => 'backend.modules.product.modules.text.frontend.ProductTextBehavior')
 *   ...
 * </pre>
 */
Yii::import('backend.modules.product.modules.text.frontend.ProductText');
/**
 * Class ProductTextBehavior
 * @property FController $owner
 */
class ProductTextBehavior extends SBehavior
{
  const UPPER = 'content_upper';

  const MAIN = 'content';

  public function getText($location = self::UPPER)
  {
    $productText = ProductText::model()->whereUrl(Yii::app()->controller->getCurrentUrl())->find();

    if( $productText )
      return $productText->{$location};

    return '';
  }
}

