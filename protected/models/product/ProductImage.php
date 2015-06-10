<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.product
 *
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

  protected $availableTypes = array('pre','big');

  public function tableName()
  {
    return '{{product_img}}';
  }
}