<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.product.behaviors
 *
 * @property Product $owner
 */

class ProductImageBehavior extends ActiveImageBehavior
{
  public function getImages($type = 'main', $onlySelected = true)
  {
    $images = parent::getImages($type);

    if( $onlySelected && $selectedColor = $this->getSelectedColor() )
    {
      $images = array_filter($images, function($item) use ($selectedColor){
        return $item->color_id == $selectedColor->color_id;
      });
    }

    return $images;
  }

  public function getSelectedColor()
  {
    if( $basketColor = $this->owner->getCollectionItems('color') )
      return $basketColor;

    if( $colorId = $this->loadColorFromSession() )
    {
      foreach($this->owner->colors as $color)
        if( $color->id == $colorId )
          return $color;
    }

    return Arr::reset($this->owner->colors);
  }

  public function saveColorInSession($colorId)
  {
    Yii::app()->session['__color_'.$this->owner->id] = intval($colorId);
  }

  private function loadColorFromSession()
  {
    $colorId = Yii::app()->session['__color_'.$this->owner->id];

    return !empty($colorId) ? intval($colorId) : null;
  }
}