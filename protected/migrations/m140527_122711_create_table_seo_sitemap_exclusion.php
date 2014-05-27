<?php

class m140527_122711_create_table_seo_sitemap_exclusion extends CDbMigration
{
	public function up()
	{
    $this->execute("CREATE TABLE IF NOT EXISTS `".$this->dbConnection->tablePrefix."seo_sitemap_exclusion` (
                                                                                                              `id` int(11) NOT NULL AUTO_INCREMENT,
                                                                                                              `route` varchar(255) NOT NULL,
                                                                                                              `lastmod` tinyint(1) NOT NULL DEFAULT '0',
                                                                                                              `changefreq` varchar(255) NOT NULL DEFAULT 'monthly',
                                                                                                              `priority` decimal(5,2) NOT NULL DEFAULT '0.00',
                                                                                                              `visible` tinyint(1) NOT NULL DEFAULT '0',
                                                                                                              PRIMARY KEY (`id`),
                                                                                                              UNIQUE KEY `route` (`route`)
                                                                                                            ) ENGINE=MyISAM DEFAULT CHARSET=koi8r AUTO_INCREMENT=1 ;");
	}

	public function down()
	{
		echo "m140527_122711_create_table_seo_sitemap_exclusion does not support migration down.\n";
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