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
 *   public function behaviors()
 *   {
 *     return CMap::mergeArray(parent::behaviors(), array(
 *       'productTextBehavior' => array('class' => 'backend.modules.product.modules.text.frontend.ProductTextBehavior')
 *     ));
 *   }
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

  private $text;

  /**
   * @param string $location
   *
   * @return string
   */
  public function getText($location = self::UPPER)
  {
    if( !isset($this->text[$location]) )
    {
      $productText = ProductText::model()->whereUrl($this->owner->getCurrentUrl())->find();

      $this->text = $productText ? $productText->{$location} : '';
    }

    return $this->text;
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
    else if( $model->canGetProperty($attribute) )
    {
      return $model->$attribute;
    }

    return '';
  }
}

