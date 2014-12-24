<?php

class m141211_144652_create_dealer_tables extends CDbMigration
{
	public function up()
	{
    $this->execute("CREATE TABLE IF NOT EXISTS `{{dealer}}` (
      `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `user_id` int(10) unsigned DEFAULT NULL,
      `name` varchar(255) NOT NULL,
      `phone` varchar(255) NOT NULL,
      `person` varchar(255) NOT NULL,
      `img` varchar(255) NOT NULL,
      `visible` tinyint(1) NOT NULL DEFAULT '0',
      PRIMARY KEY (`id`),
      KEY `user_id` (`user_id`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8");

    $this->execute("CREATE TABLE IF NOT EXISTS `{{dealer_city}}` (
        `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
        `position` int(11) DEFAULT '0',
        `name` varchar(255) NOT NULL,
        `visible` tinyint(1) DEFAULT '1',
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB  DEFAULT CHARSET=utf8");

    $this->execute("CREATE TABLE IF NOT EXISTS `{{dealer_filial}}` (
      `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `dealer_id` int(10) unsigned NOT NULL,
      `position` int(11) DEFAULT '0',
      `name` varchar(255) NOT NULL,
      `city_id` int(10) unsigned NOT NULL,
      `address` varchar(255) NOT NULL,
      `worktime` varchar(255) NOT NULL,
      `phone` varchar(255) NOT NULL,
      `phone_additional` varchar(255) NOT NULL,
      `fax` varchar(255) NOT NULL,
      `email` varchar(255) NOT NULL,
      `skype` varchar(255) NOT NULL,
      `notice` text NOT NULL,
      `site_url` varchar(255) NOT NULL,
      `coordinates` varchar(50) DEFAULT '',
      `visible` int(11) NOT NULL DEFAULT '1',
      PRIMARY KEY (`id`),
      KEY `dealer_id` (`dealer_id`),
      KEY `city_id` (`city_id`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8");
	}

	public function down()
	{
		echo "m141211_144652_create_dealer_tables does not support migration down.\n";
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