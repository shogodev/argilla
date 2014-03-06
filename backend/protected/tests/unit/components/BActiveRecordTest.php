<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 */
class BActiveRecordTest extends CDbTestCase
{
  public $fixtures = [
    'product' => 'BProduct',
  ];

  public function testDefaultListData()
  {
    $pk = 1;

    $criteria = new CDbCriteria();
    $criteria->compare('id', $pk);

    /**@var BProduct $product */
    $product = BProduct::model()->findByPk($pk);
    $result  = array(
      $product->id => $product->name,
    );

    $this->assertEquals($result, BProduct::listData('id', 'name', $criteria));
  }

  public function testCallableListData()
  {
    $pk = 1;

    $criteria = new CDbCriteria();
    $criteria->compare('id', $pk);

    /**@var BProduct $product */
    $product = BProduct::model()->findByPk($pk);
    $result  = array(
      $product->id => $product->id.'/'.$product->name,
    );

    $this->assertEquals($result, BProduct::listData('id', function (BProduct $product)
    {
      return $product->id.'/'.$product->name;
    }, $criteria));
  }
}