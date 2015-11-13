<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 *
 * 'modificationBehavior' => array('class' => 'ModificationBehavior'),
 */

/**
 * Class ModificationBehavior
 * @property Product $owner
 * @property Product[] $modifications
 * @property Product $parentModel
 */
class ModificationBehavior extends SActiveRecordBehavior
{
  private $child;

  public function init()
  {
    parent::init();

    $this->owner->getMetaData()->addRelation('modifications', array(
      FActiveRecord::HAS_MANY, 'Product', array('parent' => 'id'),
    ));

    $this->owner->getMetaData()->addRelation('parentModel', array(
      FActiveRecord::HAS_ONE, 'Product', array('id' => 'parent'),
    ));
  }

  /**
   * @return bool
   */
  public function isModification()
  {
    return !empty($this->owner->parent);
  }

  /**
   * @return Product
   */
  public function getParentModel()
  {
    return $this->owner->parentModel;
  }

  /**
   * @return Product[]
   */
  public function getModifications()
  {
    return $this->owner->modifications;
  }

  /**
   * @param $id
   *
   * @return Product|null
   */
  public function getChild($id)
  {
    if( !isset($this->child[$id]) )
      $this->child[$id] = Product::model()->findByAttributes(array('id' => $id, 'visible' => 1));

    return $this->child[$id];
  }

  /**
   * @return bool
   */
  public function isParent()
  {
    return count($this->getModifications()) > 0;
  }

  /**
   * @return Product|null
   */
  public function getFirstModification()
  {
    return Arr::reset($this->getModifications());
  }

  /**
   * Если у модификации нет изображения, метод вернет изображение родителя
   * @param string $type
   *
   * @return FActiveImage
   */
  public function getImageModification($type = 'main')
  {
    if( $this->isModification() )
    {
      if( empty($this->owner->asa('imagesBehavior')->getImage($type)->name) )
      {
        return $this->getParentModel()->asa('imagesBehavior')->getImage($type);
      }
    }
    else if( $this->isParent() )
    {
      $modification = $this->getFirstModification();
      if( !empty($modification->asa('imagesBehavior')->getImage($type)->name) )
        return $modification->asa('imagesBehavior')->getImage($type);
    }

    return $this->owner->asa('imagesBehavior')->getImage($type);
  }

  /**
   * @param string $type
   *
   * @return FActiveImage
   */
  public function getImagesModification($type = 'main')
  {
    if( $this->isModification() )
    {
      $images = $this->owner->asa('imagesBehavior')->getImages($type);
      if( empty($images) )
      {
        return $images;
      }
    }
    else if( $this->isParent() )
    {
      $modification = $this->getFirstModification();
      $images = $modification->asa('imagesBehavior')->getImages($type);
      if( !empty($images) )
        return $images;
    }

    return $this->owner->asa('imagesBehavior')->getImages($type);
  }

  /**
   * @param $property
   *
   * @return string
   */
  public function getNotEmptyProperty($property)
  {
    if( $this->isModification() && empty($this->owner->{$property}) )
    {
      return $this->getParentModel()->{$property};
    }

    return $this->owner->{$property};
  }

  public function getModificationUrl($absolute = false)
  {

    //$this->getPa
    return $absolute ? Yii::app()->createAbsoluteUrl('product/one', array('url' => $this->url)) : Yii::app()->createUrl('product/one', array('url' => $this->url));
  }
}