<?php

class m010101_000005_feel_contacts extends CDbMigration
{
	public function up()
	{
      $this->execute("INSERT INTO `" . $this->dbConnection->tablePrefix . "contact` (`id`, `name`, `sysname`, `url`, `address`, `notice`, `img`, `img_big`, `map`, `visible`) VALUES
                                                      (1, 'Контакты в шапке и футере', 'header', '', '',  '<p>Пн-Пт с 10:00 до 20:00 <span class=\"separator\">&nbsp;</span> Сб с 10:00 до 17:00</p>', '', '', '', 1);");

      $this->execute("INSERT INTO `" . $this->dbConnection->tablePrefix . "contact_group` (`id`, `sysname`, `contact_id`, `name`, `position`, `visible`) VALUES
                                                          (1, 'phones', 1, 'Телефоны', 10, 1);");

      $this->execute("INSERT INTO `" . $this->dbConnection->tablePrefix . "contact_field` (`id`, `group_id`, `value`, `description`, `position`, `visible`) VALUES
                                                          (1, 1, '555', '555-55-55', 10, 1),
                                                          (2, 1, '555', '555-55-55', 10, 1);");
	}

	public function down()
	{
		echo "m010101_000004_fee_rbac does not support migration down.\n";
		return false;
	}
}