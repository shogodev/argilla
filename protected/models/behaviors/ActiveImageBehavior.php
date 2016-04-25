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
 * @property FActiveImage image;
 * @property FActiveImage images;
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
      $criteria = $this->getColorCriteria();
      $criteria->compare('parent', $this->owner->getPrimaryKey());
      /**
       * @var FActiveRecord $model
       */
      $model = new $this->imageClass;
      $images = $model->findAll($criteria);

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

  /**
   * Возвращает галерею изображений
   *
   * @param FActiveImage|null $excludeImage - исключить изображение
   * @param array $types - доступные типы
   *
   * @return FActiveImage[]
   */
  public function getImagesGallery(FActiveImage $excludeImage = null, $types = array('main', 'gallery'))
  {
    $galleryImages = array();

    foreach($types as $type)
    {
      foreach($this->getImages($type) as $image)
      {
        if( $image->id != $excludeImage->id )
          $galleryImages[] = $image;
      }
    }

    return $galleryImages;
  }

  public function getColorCriteria()
  {
    $criteria = new CDbCriteria();
    $criteria->order = 'IF(position = 0, 1, 0), position';

    if( !Yii::app()->controller->asa('productFilterBehavior') )
      return $criteria;

    /**
     * @var ProductFilterBehavior $controller
     */
    $controller = Yii::app()->controller;
    if( $filter = $controller->getFilter(false) )
    {
      if( $color = $filter->getElementByKey(ProductFilterBehavior::FILTER_COLOR) )
      {
        /**
         * @var FilterElementItem $selectedItem
         */
        if( $selectedItem = Arr::reset($color->getSelectedItems()) )
        {
          $criteria->order = 'IF(notice = '.intval($selectedItem->id).', 0, 1), IF(position = 0, 1, 0), position';
        }
      }
    }

    return $criteria;
  }
}