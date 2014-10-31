<?php

class m010101_000008_init_notification extends CDbMigration
{
	public function up()
	{
    $this->execute("INSERT INTO `{{notification}}` (`id`, `index`, `name`, `email`, `subject`, `view`, `message`, `visible`)
      VALUES (NULL , 'Order', 'Заказ', '', 'Вы сделали заказ на сайте {projectName}', 'order', '', 0)");

    $this->execute("INSERT INTO `{{notification}}` (`id`, `index`, `name`, `email`, `subject`, `view`, `message`, `visible`)
      VALUES (NULL , 'OrderBackend', 'Заказ (Backend)', '', 'Пользователь сделал заказ на сайте {projectName}', 'orderBackend', '', 0)");

    $this->execute("INSERT INTO `{{notification}}` (`id`, `index`, `name`, `email`, `subject`, `view`, `message`, `visible`)
      VALUES (NULL , 'FastOrderBackend', 'Быстрый заказ (Backend)', '', 'Пользователь сделал быстрый заказ на сайте {projectName}', 'fastOrderBackend', '', 0)");

    $this->execute("INSERT INTO `{{notification}}` (`id`, `index`, `name`, `email`, `subject`, `view`, `message`, `visible`)
      VALUES (NULL , 'CallbackBackend', 'Заказ обратного звонка (Backend)', '', 'Заказ обратного звонка на сайте {projectName}', 'defaultBackend', '', 0)");

    $this->execute("INSERT INTO `{{notification}}` (`id`, `index`, `name`, `email`, `subject`, `view`, `message`, `visible`)
      VALUES (NULL , 'OrderConfirmedBackend', 'Заказ подтвержден', '', 'Ваш заказ подтвержден на сайте {projectName}', 'orderConfirmed', '', 0)");

    $this->execute("INSERT INTO `{{notification}}` (`id`, `index`, `name`, `email`, `subject`, `view`, `message`, `visible`)
      VALUES (NULL , 'OrderCanceledBackend', 'Заказ отменен', '', 'Ваш заказ отменен на сайте {projectName}', 'orderCanceled', '', 0)");

    $this->execute("INSERT INTO `{{notification}}` (`id`, `index`, `name`, `email`, `subject`, `view`, `message`, `visible`)
      VALUES (NULL , 'UserRegistration', 'Регистрация пользователя', '', 'Регистрация на сайте {projectName}', 'userRegistration', '', 0)");

    $this->execute("INSERT INTO `{{notification}}` (`id`, `index`, `name`, `email`, `subject`, `view`, `message`, `visible`)
      VALUES (NULL , 'UserRegistrationBackend', 'Регистрация пользователя (Backend)', '', 'Регистрация пользователя на сайте {projectName}', 'userRegistrationBackend', '', 0)");

    $this->execute("INSERT INTO `{{notification}}` (`id`, `index`, `name`, `email`, `subject`, `view`, `message`, `visible`)
      VALUES (NULL , 'UserRequestRestorePassword', 'Запрос на восстановление пароля пользователя', '', 'Восстановление пароля на сайте {projectName}', 'userRequestRestorePassword', '', 0)");

    $this->execute("INSERT INTO `{{notification}}` (`id`, `index`, `name`, `email`, `subject`, `view`, `message`, `visible`)
      VALUES (NULL , 'UserRestorePassword', 'Восстановление пароля пользователя', '', 'Восстановление пароля на сайте {projectName}', 'userRestorePassword', '', 0)");

    $this->execute("INSERT INTO `{{notification}}` (`id`, `index`, `name`, `email`, `subject`, `view`, `message`, `visible`)
      VALUES (NULL , 'UserChangePassword', 'Смена пароля пользователя', '', 'Смена пароля на сайте {projectName}', 'userChangePassword', '', 0)");
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