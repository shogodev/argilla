<?php

class m010101_000013_platron extends CDbMigration
{
	public function up()
	{
    $this->execute("INSERT INTO `{{platron_payment_type}}` (`id`, `key`, `name`, `position`, `notice`, `img`, `visible`) VALUES
      (1, 'RUSSIANSTANDARD', 'Банковские карты', 0, '(VISA, MasterCard) ', 'pay-icon-visa.png', 1),
      (2, 'CASH', 'Наличные в кассах и платежных терминалах', 0, '', 'pay-icon-cash.png', 1),
      (3, 'ALFACLICK', 'Интернет-банкинг Альфа-Клик', 0, '', 'pay-icon-alpha.png', 1),
      (4, 'YANDEXMONEY', 'Яндекс.Деньги', 0, '', 'pay-icon-yandex.png', 1),
      (5, 'WEBMONEYRBANK', 'Webmoney', 0, '', 'pay-icon-webmoney.png', 1),
      (6, 'MONEYMAILRU', 'Деньги@Mail.ru', 0, '', 'pay-icon-mailru.png', 1),
      (7, 'MOBILEPHONE', 'Оплата с мобильного телефона', 0, '', 'pay-icon-mobile.png', 1),
      (8, 'FAKTURA', 'Интернет-банкинг Faktura.ru', 0, '', 'pay-icon-faktura.png', 1),
      (9, 'W1RUR', 'Единый кошелёк - W1', 0, NULL, '', 0),
      (10, 'PSB', 'Интернет-банкинг Промсвязьбанк ', 0, NULL, '', 0);");
	}

	public function down()
	{
		echo "m010101_000013_platron does not support migration down.\n";
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