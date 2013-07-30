<?php
/**
 * @property integer $id
 * @property integer $parent

 * @property string $name
 * @property string $size
 * @property string $type
 * @property string $notice
 * @property integer $position
 *
 * @method static ProductImage model(string $class = __CLASS__)
 */
class ProductImage extends FActiveImage
{
  protected $imageDir = 'f/product/';

  protected $availableTypes = array('pre');

  public function tableName()
  {
    return '{{product_img}}';
  }
}