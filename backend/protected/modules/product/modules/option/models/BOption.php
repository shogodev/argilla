<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 */

/**
 * @method static BOption model(string $class = __CLASS__)
 *
 * @property integer $id
 * @property integer $position
 * @property integer $product_id
 * @property string $name
 * @property string $price
 * @property string $image
 * @property string $content
 * @property string $visible
 *
 * @property BProduct $product
 */
class BOption extends BActiveRecord
{
  /**
   * @var
   */
  private $productOptions;

  public function tableName()
  {
    return '{{product_option}}';
  }

  public function behaviors()
  {
    return array('uploadBehavior' => array('class' => 'UploadBehavior', 'validAttributes' => 'img'));
  }

  public function rules()
  {
    return array(
      array('product_id, name', 'required'),
      array('position, product_id, visible', 'numerical', 'integerOnly' => true),
      array('content, price', 'safe'),
    );
  }

  public function relations()
  {
    return array(
      'product' => array(self::BELONGS_TO, 'BProduct', 'product_id'),
    );
  }

  public function getByProductId($productId)
  {
    if( is_null($this->productOptions) )
    {
      $this->productOptions = $this->findAllByAttributes(array('product_id' => $productId));
    }

    return $this->productOptions;
  }
}