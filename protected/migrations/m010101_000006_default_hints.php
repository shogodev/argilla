<?php

class m010101_000006_default_hints extends CDbMigration
{
	public function up()
	{
      $this->execute("INSERT INTO `".$this->dbConnection->tablePrefix."hint` (`id`, `model`, `attribute`, `content`, `popup`) VALUES
                      (1, 'BMetaRoute', 'title', 'Для приведения переменных к регистру доступны функции:<br/><b>ucfirst{переменная}</b> - возводит первую букву в верхний регистр; <br/><b>upper{переменная}</b> - возводит в верхний регистр; <br/><b>lower{переменная}</b> - приводит к нижниму регистру; ', 0),
                      (2, 'BRedirect', 'base', 'Можно задавать регулярное выражение. Формат: <b>#/komplekty/(\\d+)#</b>', 0),
                      (3, 'BRedirect', 'target', 'Можно получить параметры из регулярного выражения. Формат: <b>/aksessuary_dlya_lyzh/$1</b>', 0);
                      (4, 'BMetaMask', 'url_mask', 'Относительная ссылка (/parketnaya_doska/price/0-1500/) или регулярное выражение (#^/\w+/price/[\d\-]+/$#)', 0);
                      ");
	}

	public function down()
	{
		echo "m010101_000006_default_hints does not support migration down.\n";
		return false;
	}
}