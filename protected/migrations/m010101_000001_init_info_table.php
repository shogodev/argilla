<?php

class m010101_000001_init_info_table extends CDbMigration
{
	public function up()
	{
    Yii::app()->db->createCommand()->insert('{{info}}', array('id'      => 1,
                                                              'lft'     => 1,
                                                              'rgt'     => 2,
                                                              'level'   => 1,
                                                              'name'    => 'Информационные страницы',
                                                              'url'     => 'root',
                                                              'visible' => 0,
                                                              'sitemap' => 0,
                                                             ));
	}

	public function down()
	{
		echo "m010101_000001_init_info_table does not support migration down.\n";
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