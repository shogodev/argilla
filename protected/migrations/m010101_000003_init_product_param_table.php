<?php

class m010101_000003_init_product_param_table extends CDbMigration
{
	public function up()
	{
    Yii::app()->db->createCommand()->insert('{{product_param_name}}', array('id' => 1, 'parent' => '1', 'type' => ''));
	}

	public function down()
	{
		echo "m010101_000003_init_product_param_table does not support migration down.\n";
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