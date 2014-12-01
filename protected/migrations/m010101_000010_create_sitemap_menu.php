<?php

class m010101_000010_create_sitemap_menu extends CDbMigration
{
	public function up()
	{
    $this->execute("INSERT INTO `{{menu}}` (`name`, `sysname`, `url`, `visible`) VALUES ('Карта сайта', 'site_map', '', 1)");
	}

	public function down()
	{
		echo "m010101_000010_create_sitemap_menu does not support migration down.\n";
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