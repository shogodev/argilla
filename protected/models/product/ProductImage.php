<?php

/**
 * @property integer $id
 * @property integer $parent

 * @property string $name
 * @property string $size
 * @property string $type
 * @property string $notice

 * @property integer $position
 */
class ProductImage extends FActiveImage
{
  protected $folder = 'f/product/';

  protected $availableTypes = array('pre');

  public function tableName()
  {
    return '{{product_img}}';
  }

  public function getPath()
  {
    return $this->folder;
  }
}