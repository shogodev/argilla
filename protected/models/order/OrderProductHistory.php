<?php
/**
 * @author    Vladimir Utenkov <utenkov@shogo.ru>
 * @link      https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license   http://argilla.ru/LICENSE
 */
/**
 * Class OrderProductHistory
 *
 * @property int    $order_product_id
 * @property int    $product_id
 * @property string $url
 * @property string $img
 * @property string $articul
 */
class OrderProductHistory extends FActiveRecord
{
  public function getDbConnection()
  {
    return Yii::app()->commonDB;
  }

  public function rules()
  {
    return [
      ['order_product_id, product_id', 'required'],
      ['url, img, articul', 'safe'],
    ];
  }
}