<?php
/**
 * @author Nikita Melnikov <n.melnikov@dengionline.com>
 * @link skype: xnicksevenfoldx
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

		/**@var BProduct $product*/
		$product = BProduct::model()->findByPk($pk);
		$result = array(
			$product->id => $product->name,
		);

		$this->assertEquals($result, BProduct::listData('id', 'name', $criteria));
	}

	public function testCallableListData()
	{
		$pk = 1;

		$criteria = new CDbCriteria();
		$criteria->compare('id', $pk);

		/**@var BProduct $product*/
		$product = BProduct::model()->findByPk($pk);
		$result = array(
			$product->id => $product->id.'/'.$product->name,
		);

		$this->assertEquals($result, BProduct::listData('id', function(BProduct $product){
					return $product->id.'/'.$product->name;
				}, $criteria));
	}
}