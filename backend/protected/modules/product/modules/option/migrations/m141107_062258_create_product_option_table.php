<?php
/**
 * Команда миграции:
 * protected/yiic migrate up --migrationPath=backend.modules.product.modules.option.migrations
 */

class m141107_062258_create_product_option_table extends CDbMigration
{
	public function up()
	{
    $this->execute("CREATE TABLE IF NOT EXISTS `{{product_option}}` (
      `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `position` int(11) DEFAULT '0',
      `product_id` int(10) unsigned NOT NULL,
      `name` varchar(255) NOT NULL,
      `price` decimal(10,2) DEFAULT NULL,
      `img` varchar(255) NOT NULL,
      `content` text,
      `visible` tinyint(1) NOT NULL DEFAULT '1',
      PRIMARY KEY (`id`),
      KEY `product_options_ibfk_1` (`product_id`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8");
	}

	public function down()
	{
		echo "m141107_062258_create_product_option_table does not support migration down.\n";
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