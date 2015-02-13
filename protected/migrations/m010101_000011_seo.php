<?php

class m010101_000011_seo extends CDbMigration
{
	public function up()
	{
    $this->execute("INSERT INTO `{{seo_link_block}}` (`id`, `name`, `code`, `url`, `key`, `position`, `visible`) VALUES (1, 'Copyright', '<p>© Copyright «argilla.ru» 2015. Все права защищены.</p>', '*', 'copyright', 10, 1);");
    $this->execute("INSERT INTO `{{seo_counters}}` (`id`, `name`, `code`, `main`, `visible`) VALUES
        (1, 'Счетчик 1', '<img src=\"i/hotlog.gif\"/>', 0, 1),
        (2, 'Счетчик 2', '<img src=\"i/hotlog.gif\"/>', 0, 1);");
	}

	public function down()
	{
		echo "m010101_000011_seo does not support migration down.\n";
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