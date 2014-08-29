<?php

class m010101_000006_default_hints extends CDbMigration
{
	public function up()
	{
      $this->execute("INSERT INTO `".$this->dbConnection->tablePrefix."hint` (`id`, `model`, `attribute`, `content`, `popup`) VALUES
                      (1, 'BMetaRoute', 'title', 'Доступны следующие функции:<br/><b>ucfirst({переменная})</b> - первую букву в верхний регистр; <br/><b>upper({переменная})</b> - в верхний регистр; <br/><b>lower({переменная})</b> - в нижний регистр; <br/><b>implode(-, {переменная1}, {переменная2})</b> - склеивает через указанный символ; <br/><b>wrap({переменная}, /, /)</b> - оборачивает в произвольные символы;', 0),
                      (2, 'BRedirect', 'base', 'Можно задавать регулярное выражение. Формат: <b>#/komplekty/(\d+)#</b>', 0),
                      (3, 'BRedirect', 'target', 'Можно задавать регулярное выражение. Формат: <b>#/aksessuary_dlya_lyzh/(\d+)#</b>', 0);
                      (4, 'BMetaMask', 'url_mask', 'Относительная ссылка (/parketnaya_doska/price/0-1500/) или регулярное выражение (#^/\w+/price/[\d\-]+/$#)', 0);
                      ");
	}

	public function down()
	{
		echo "m010101_000006_default_hints does not support migration down.\n";
		return false;
	}
}