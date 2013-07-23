<?php

class m010101_000008_init_notification extends CDbMigration
{
	public function up()
	{
    $this->execute("INSERT INTO `{{notification}}` (`id`, `index`, `name`, `email`, `subject`, `view`, `message`, `visible`)
      VALUES (NULL , 'UserRegistration', 'Регистрация пользователя', '', 'Регистрация на сайте {projectName}', 'userRegistration', '', 0)");

    $this->execute("INSERT INTO `{{notification}}` (`id`, `index`, `name`, `email`, `subject`, `view`, `message`, `visible`)
      VALUES (NULL , 'UserRegistrationBackend', 'Регистрация пользователя (для менеджера)', '', 'Регистрация пользователя на сайте {projectName}', 'userRegistrationBackend', '', 0)");

    $this->execute("INSERT INTO `{{notification}}` (`id`, `index`, `name`, `email`, `subject`, `view`, `message`, `visible`)
      VALUES (NULL , 'UserRequestRestorePassword', 'Запрос на восстановление пароля пользователя', '', 'Восстановление пароля на сайте {projectName}', 'userRequestRestorePassword', '', 0)");

    $this->execute("INSERT INTO `{{notification}}` (`id`, `index`, `name`, `email`, `subject`, `view`, `message`, `visible`)
      VALUES (NULL , 'UserRestorePassword', 'Восстановление пароля пользователя', '', 'Восстановление пароля на сайте {projectName}', 'userRestorePassword', '', 0)");

    $this->execute("INSERT INTO `{{notification}}` (`id`, `index`, `name`, `email`, `subject`, `view`, `message`, `visible`)
      VALUES (NULL , 'UserChangePassword', 'Смена пароля пользователя', '', 'Смена пароля на сайте {projectName}', 'userChangePassword', '', 0)");

    $this->execute("INSERT INTO `{{notification}}` (`id`, `index`, `name`, `email`, `subject`, `view`, `message`, `visible`)
      VALUES (NULL , 'Order', 'Пользователь сделал заказ', '', 'Вы сделали заказ на сайте {projectName}', 'order', '', 0)");

    $this->execute("INSERT INTO `{{notification}}` (`id`, `index`, `name`, `email`, `subject`, `view`, `message`, `visible`)
      VALUES (NULL , 'OrderBackend', 'Пользователь сделал заказ (для менеджера)', '', 'Пользователь сделал заказ на сайте {projectName}', 'orderBackend', '', 0)");

    $this->execute("INSERT INTO `{{notification}}` (`id`, `index`, `name`, `email`, `subject`, `view`, `message`, `visible`)
      VALUES (NULL , 'Callback', 'Пользователь сделал заказ обратного звонка', '', 'Заказ обратного звонка на сайте {projectName}', 'callback', '', 0)");
	}

	public function down()
	{
		echo "m010101_000008_init_notification does not support migration down.\n";
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