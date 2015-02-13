<?php

class m010101_000012_create_news_section extends CDbMigration
{
	public function up()
	{
    $this->execute("INSERT INTO `{{news_section}}` (`id`, `position`, `url`, `name`, `notice`, `img`, `visible`) VALUES
      (1, 0, 'news', 'Новости', '', NULL, 1),
      (2, 0, 'articles', 'Статьи', '', NULL, 1);");
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