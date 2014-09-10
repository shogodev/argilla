<?php

class m140905_123425_create_cackle_tables extends CDbMigration
{
	public function up()
	{
    $this->execute("CREATE TABLE {{cackle_comment}} (
          id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
          channel TEXT NOT NULL,
          url VARCHAR(255) NOT NULL DEFAULT '',
          comment TEXT NOT NULL,
          rating INT(11) NOT NULL,
          date DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
          author VARCHAR(255) NOT NULL DEFAULT '',
          email VARCHAR(255) NULL DEFAULT '',
          avatar VARCHAR(255) NOT NULL,
          ip VARCHAR(255) NOT NULL DEFAULT '',
          status VARCHAR(255) NOT NULL,
          modified VARCHAR(255) NOT NULL,
          PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

    $this->execute("CREATE TABLE {{cackle_review}} (
          id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
          channel VARCHAR(255) NOT NULL DEFAULT '',
          url VARCHAR(255) NOT NULL DEFAULT '',
          dignity TEXT NOT NULL,
          lack TEXT NOT NULL,
          comment TEXT NOT NULL,
          rating INT(11) NOT NULL,
          stars INT(11) NOT NULL,
          date DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
          author VARCHAR(255) NOT NULL DEFAULT '',
          email VARCHAR(255) NOT NULL DEFAULT '',
          avatar VARCHAR(255) NOT NULL,
          ip VARCHAR(255) NOT NULL DEFAULT '',
          status VARCHAR(255) NOT NULL,
          modified VARCHAR(255) NOT NULL,
          PRIMARY KEY (id)
          )ENGINE=InnoDB DEFAULT CHARSET=utf8;");
	}

	public function down()
	{
		echo "m140905_123425_create_cackle_tables does not support migration down.\n";
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