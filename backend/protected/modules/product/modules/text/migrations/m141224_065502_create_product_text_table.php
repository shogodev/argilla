<?php
/**
 * Команда миграции:
 * protected/yiic migrate up --migrationPath=backend.modules.product.modules.text.migrations
 */

/**
 * Class m141224_065502_create_product_text_table
 */
class m141224_065502_create_product_text_table extends CDbMigration
{
	public function up()
	{
    $this->execute("CREATE TABLE IF NOT EXISTS `{{product_text}}` (
      `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `name` varchar(255) DEFAULT NULL,
      `url` varchar(255) DEFAULT NULL,
      `visible` tinyint(1) DEFAULT '0',
      `content` text,
      `content_upper` text,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;");
	}

	public function down()
	{
		echo "m141224_065502_create_product_text_table does not support migration down.\n";
		return false;
	}
}