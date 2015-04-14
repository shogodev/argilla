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
        `product_id` int(10) unsigned NOT NULL,
        `tag` varchar(255) CHARACTER SET utf8 NOT NULL,
        UNIQUE KEY `product_id_2` (`product_id`,`tag`),
        KEY `product_id` (`product_id`),
        KEY `tag` (`tag`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");


    $this->execute("ALTER TABLE `{{product_common_association}}`
        ADD CONSTRAINT `{{product_common_association_ibfk_1}}` FOREIGN KEY (`product_id`) REFERENCES `{{product}}` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;");
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