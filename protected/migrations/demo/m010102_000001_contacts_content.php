<?php

class m010102_000001_contacts_content extends CDbMigration
{
	public function up()
	{
    $this->execute("INSERT INTO `{{contact}}` (`id`, `name`, `sysname`, `url`, `address`, `notice`, `img`, `img_big`, `map`, `visible`) VALUES
      (2, 'ООО «Аргила»', 'contacts', 'www.argiila.ru', '<p class=\"s14\">153002, Москва, ул. Рязанская д. 20, корп. 2</p>\r\n', '<div class=\"m15\">\r\n<p class=\"bb m5\">Время работы:</p>\r\n\r\n<p class=\"s14\">пн-пт 10:00-20:00<br />\r\nсб-вс по предварительному звонку</p>\r\n</div>\r\n', '', '', '', 1)");
	}

	public function down()
	{
    $this->execute("DELETE FROM `{{contact}}` WHERE id IN (2)");

    return true;
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