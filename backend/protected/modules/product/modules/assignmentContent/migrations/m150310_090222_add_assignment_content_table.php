<?php

class m150310_090222_add_assignment_content_table extends CDbMigration
{
	public function up()
	{
    $this->execute("CREATE TABLE IF NOT EXISTS `{{product_assignment_content}}` (
      `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
      `section_id` int(10) unsigned NOT NULL,
      `type_id` int(10) unsigned NOT NULL,
      `category_id` int(10) unsigned NOT NULL,
      `collection_id` int(10) unsigned NOT NULL,
      `location` VARCHAR( 255 ) NOT NULL,
       `content` TEXT NOT NULL,
      `visible` tinyint(1) NOT NULL,
      PRIMARY KEY (`id`),
      KEY `section_id` (`section_id`),
      KEY `category_id` (`category_id`),
      KEY `collection_id` (`collection_id`),
      KEY `type_id` (`type_id`),
      KEY `location` (`location`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;");
	}

	public function down()
	{
		echo "m150310_090222_add_assignment_content_table does not support migration down.\n";
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