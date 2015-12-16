<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.behaviors
 */

/**
 * Behavior realised methods for get/set/cache FActiveImage objects of FActiveRecord model
 *
 * Example:
 *
 * <pre>
 * public function behaviors()
 * {
 *   return array(
 *     'imagesBehavior' => array('class' => 'ActiveImageBehavior', 'imageClass' => 'ProductImage'),
 *   );
 * }
 * </pre>
 *
 * Class ActiveImageBehavior
 *
 * @property FActiveRecord $owner
 */
class ActiveImageBehavior extends SBehavior
{
  public $imageClass;

  private $images;

  public function init()
  {
    if( !isset($this->imageClass) )
    {
      throw new CException('Can not attach ActiveImageBehavior without imageClass property');
    }
  }

  /**
   * @param string $type
   *
   * @return FActiveImage
   */
  public function getImage($type = 'main')
  {
    if( $image = Arr::reset($this->getImages($type)) )
      return $image;

    return Yii::createComponent(['class' => $this->imageClass]);
  }

  /**
   * @param string $type
   *
   * @return FActiveImage[]
   */
  public function getImages($type = 'main')
  {
    if( !isset($this->images) )
    {
      /**
       * @var FActiveRecord $model
       */
      $model = call_user_func(array($this->imageClass, 'model'));

      $images = $model->findAllByAttributes(
        array('parent' => $this->owner->getPrimaryKey()),
        array('order' => 'IF(position, position, 999999999)')
      );

      $this->setImages($images, $type);
    }

    return isset($this->images[$type]) ? $this->images[$type] : array();
  }

  /**
   * @param array  $images
   * @param string $type
   *
   * @return void
   */
  public function setImages($images, $type)
  {
    if( !isset($this->images) )
      $this->images = array();

    if( !isset($this->images[$type]) )
      $this->images[$type] = array();

    foreach($images as $image)
      $this->images[$image['type']][] = $image;
  }
}
