<?php

class m010101_000004_init_rbac extends CDbMigration
{
	public function up()
	{
      $this->execute("INSERT INTO `" . $this->dbConnection->tablePrefix . "auth_user` (`id`, `username`, `password`, `visible`) VALUES
                                                                                      (2, 'content', '', 1),
                                                                                      (3, 'seo', '', 1);");

      $this->execute("INSERT INTO `" . $this->dbConnection->tablePrefix . "auth_item` (`id`, `name`, `title`, `type`, `description`, `bizrule`, `data`) VALUES
                                                                                      (null, 'admin', 'Администратор', 2, '', NULL, NULL),
                                                                                      (null,'content', 'Контент', 2, '', NULL, NULL),
                                                                                      (null,'seo', 'SEO', 2, '', NULL, NULL)");

      $this->execute("INSERT INTO `" . $this->dbConnection->tablePrefix . "auth_assignment` (`id`,`itemname`, `userid`, `bizrule`, `data`) VALUES
                                                                                            (null,'content', '2', NULL, 'N;'),
                                                                                            (null,'seo', '3', NULL, 'N;');");
	}

	public function down()
	{
		echo "m010101_000004_init_rbac does not support migration down.\n";
		return false;
	}
}