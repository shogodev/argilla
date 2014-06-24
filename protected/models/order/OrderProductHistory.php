<?php
/**
 * @author Vladimir Utenkov <utenkov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.order
 *
 * Class OrderProductHistory
 *
 * @property int $order_product_id
 * @property int $product_id
 * @property string $url
 * @property string $img
 * @property string $articul
 *
 * @property Product $product
 */
class OrderProductHistory extends FActiveRecord
{
  public function rules()
  {
    return [
      ['order_product_id, product_id', 'required'],
      ['url, img, articul', 'safe'],
    ];
  }

  public function relations()
  {
    return array(
      'product' => array(self::BELONGS_TO, 'Product', 'product_id'),
    );
  }
}