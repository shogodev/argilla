<?php
/**
 * Команда миграции:
 * protected/yiic migrate up --migrationPath=backend.modules.product.modules.group.migrations
 */

class m141111_145613_create_product_group_table extends CDbMigration
{
	public function up()
	{
    $this->execute("CREATE TABLE IF NOT EXISTS `{{product_group}}` (
      `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `position` int(11) NOT NULL,
      `name` varchar(255) NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8");
	}

	public function down()
	{
		echo "m141111_145613_create_product_group_table does not support migration down.\n";
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