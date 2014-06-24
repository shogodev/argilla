<?php

class m010101_000009_dir_data extends CDbMigration
{
	public function up()
	{
    $this->execute("INSERT INTO `{{order_payment_type}}` (`id`, `name`, `position`, `notice`, `visible`) VALUES
      (1, 'Оплата наличными', 0, NULL, 1),
      (2, 'Безналичный платеж', 0, NULL, 1),
      (3, 'Электронные деньги', 0, NULL, 1)");

    $this->execute("INSERT INTO `{{order_delivery_type}}` (`id`, `name`, `position`, `notice`, `visible`) VALUES
      (1, 'Самовывоз', 2, NULL, 1),
      (2, 'Доставка', 1, NULL, 1)");

    $this->execute("INSERT INTO `{{order_status}}` (`id`, `name`, `sysname`) VALUES
      (null, 'Оформлен', 'new'),
      (null, 'Обработан менеджером', 'confirmed'),
      (null, 'Ожидает оплаты', 'wait_payment'),
      (null, 'Оплачен', 'paid'),
      (null, 'Доставлен', 'delivered'),
      (null, 'Отменен', 'canceled')");
	}

	public function down()
	{
		echo "m010101_000009_dir_data not support migration down.\n";
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