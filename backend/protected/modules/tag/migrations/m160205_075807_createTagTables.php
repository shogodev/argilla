<?php
/**
 * Команда миграции:
 * protected/yiic migrate up --migrationPath=backend.modules.tag.migrations
 */

class m160205_075807_createTagTables extends CDbMigration
{
	public function up()
	{
		$this->execute("CREATE TABLE IF NOT EXISTS `{{tag}}` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `name` varchar(255) NOT NULL COMMENT 'Тег',
			  `group` varchar(255) NOT NULL COMMENT 'Группа',
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `name_UNIQUE` (`name`),
			  KEY `group` (`group`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8;");

		$this->execute("CREATE TABLE IF NOT EXISTS `{{tag_item}}` (
			`item_id` int(10) unsigned NOT NULL,
		  `tag_id` int(11) NOT NULL,
		  `group` varchar(255) NOT NULL,
		  PRIMARY KEY (`tag_id`,`item_id`),
		  KEY `fk_product_tag` (`tag_id`),
		  KEY `item_id` (`item_id`,`tag_id`,`group`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

		$this->execute("ALTER TABLE `{{tag_item}}` ADD CONSTRAINT `fk_product_tag` FOREIGN KEY (`tag_id`) REFERENCES `{{tag}}` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;");
	}

	public function down()
	{
		echo "m160205_075807_createTagTable does not support migration down.\n";
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