<?php

class m010101_000016_instagram_settings extends CDbMigration
{
	public function up()
	{
    $this->execute("INSERT INTO `" . $this->dbConnection->tablePrefix . "settings` (`param`, `value`, `notice`) VALUES
     																																	('instagram', 'empty', 'Настройки вида {\"clientId\": \"%clientId%\", \"userId\": \"%userId%\", \"accessToken\": \"%accessToken%\"}. Получить accessToken можно здесь http://instagram.pixelunion.net/');");
	}

	public function down()
	{
		echo "m010101_000016_instagram_settings does not support migration down.\n";
		return false;
	}
}
