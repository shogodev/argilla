<?php
Yii::import('backend.modules.product.modules.text.migrations.*');

class m010101_000015_submodules extends CDbMigration
{
	public function up()
	{
    $migration = new m141224_065502_create_product_text_table();
    $migration->up();
	}

	public function down()
	{
		echo "m010101_000015_submodules does not support migration down.\n";
		return false;
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}