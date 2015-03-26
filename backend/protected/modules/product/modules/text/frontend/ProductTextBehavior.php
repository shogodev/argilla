<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
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

  /**
   * @param string $location
   *
   * @return string
   */
  public function getText($location = self::UPPER)
  {
    $productText = ProductText::model()->whereUrl($this->owner->getCurrentUrl())->find();
    return $productText ? $productText->{$location} : '';
  }

  /**
   * @return string
   */
  public function getBottomText()
  {
    return $this->getText(self::MAIN);
  }

  /**
   * @param FActiveRecord $model
   * @param string $attribute
   * @param string $location
   *
   * @return string
   */
  public function getModelText(FActiveRecord $model, $attribute = 'notice', $location = self::UPPER)
  {
    if( $text = $this->getText($location) )
    {
      return $text;
    }
    else
    {
      return $model->$attribute;
    }
  }
}

