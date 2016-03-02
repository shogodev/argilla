<?php
/**
 * protected/yiic migrate up --migrationPath=frontend.extensions.retailcrm.migrations
 */
class m160220_081308_add_field_retail_crm_url extends CDbMigration
{
	public function up()
	{
		$this->execute('ALTER TABLE  `{{order}}` ADD  `retail_crm_url` VARCHAR( 255 ) NOT NULL AFTER  `order_comment`');
		$this->execute('ALTER TABLE  `{{callback}}` ADD  `retail_crm_url` VARCHAR( 255 ) NOT NULL');
	}

	public function down()
	{
		echo "m160220_081308_add_field_retail_crm_url does not support migration down.\n";
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