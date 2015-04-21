<?php

class m010101_000014_dump extends CDbMigration
{
	public function up()
	{
    $this->execute("INSERT INTO `{{product_dump}}` (`id`, `name`, `description`) VALUES (0, 'нет в наличии', ''), (1, 'в наличии', '')");
	}

	public function down()
	{
		echo "m010101_000014_dump does not support migration down.\n";
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