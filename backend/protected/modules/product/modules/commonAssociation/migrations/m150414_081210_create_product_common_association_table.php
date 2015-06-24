<?php
/**
 * Пример запуска:
 * protected/yiic migrate up --migrationPath=backend.modules.product.modules.commonAssociation.migrations
 */

/**
 * Class m150414_081210_create_product_common_association_table
 */
class m150414_081210_create_product_common_association_table extends CDbMigration
{
	public function up()
	{
    $this->execute("CREATE TABLE IF NOT EXISTS `{{product_common_association}}` (
        `pk` int(10) unsigned NOT NULL,
        `tag` varchar(255) NOT NULL,
        `association_group` varchar(255) NOT NULL DEFAULT 'default',
        UNIQUE KEY `pk_2` (`pk`,`tag`),
        KEY `pk` (`pk`),
        KEY `tag` (`tag`),
        KEY `association_group` (`association_group`),
        KEY `tag_2` (`tag`,`association_group`),
        KEY `pk_3` (`pk`,`association_group`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

    $this->execute("ALTER TABLE `{{product_common_association}}` ADD CONSTRAINT `{{product_common_association_ibfk_1}}` FOREIGN KEY (`pk`) REFERENCES `{{product}}` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;");
	}

	public function down()
	{
		echo "m150414_081210_create_product_common_association_table does not support migration down.\n";
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