<?php
/**
 * Команда миграции:
 * protected/yiic migrate up --migrationPath=backend.modules.product.modules.color.migrations
 */
class m141216_085329_color_tables extends CDbMigration
{
	public function up()
	{
    $this->execute("CREATE TABLE IF NOT EXISTS `{{color}}` (
      `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `variant_id` int(10) unsigned DEFAULT NULL,
      `name` varchar(255) NOT NULL,
      `img` varchar(255) NOT NULL,
      PRIMARY KEY (`id`),
      KEY `group_id` (`variant_id`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8");

    $this->execute("ALTER TABLE `{{color}}` ADD CONSTRAINT `{{color_ibfk_1}}` FOREIGN KEY (`variant_id`) REFERENCES `{{product_param_variant}}` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;");

    $this->execute("CREATE TABLE IF NOT EXISTS `{{product_color}}` (
      `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `position` int(11) NOT NULL,
      `product_id` int(10) unsigned NOT NULL,
      `color_id` int(10) unsigned NOT NULL,
      `visible` tinyint(1) NOT NULL DEFAULT '0',
      PRIMARY KEY (`id`),
      KEY `product_id` (`product_id`),
      KEY `color_id` (`color_id`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8");

    $this->execute("ALTER TABLE `{{product_color}}` ADD CONSTRAINT `{{product_color_ibfk_1}}` FOREIGN KEY (`color_id`) REFERENCES `{{color}}` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;");
	}

	public function down()
	{
		echo "m141216_085329_color_tables does not support migration down.\n";
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