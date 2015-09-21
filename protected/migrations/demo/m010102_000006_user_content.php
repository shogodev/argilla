<?php
Yii::import('frontend.components.auth.FUserIdentity');

class m010102_000006_user_content extends CDbMigration
{
  static $orderProductCount = 1;

	public function up()
	{
    $passHash = FUserIdentity::createPassword('test', '123');

    $this->execute("INSERT INTO `{{user}}` (`id`, `date_create`, `login`, `password_hash`, `email`, `restore_code`, `type`, `visible`)
        VALUES (1, '2015-04-01 12:50:00', 'test', '".$passHash."', 'test@test.ru', '', 'user', 1)");

    $this->execute("INSERT INTO `{{user_profile}}` (`user_id`, `name`, `last_name`, `patronymic`, `address`, `phone`, `birthday`)
        VALUES (1, 'Виктор', '', '', '', '', '0000-00-00');");

    $passHash2 = FUserIdentity::createPassword('test2', '123');

    $this->execute("INSERT INTO `{{user}}` (`id`, `date_create`, `login`, `password_hash`, `email`, `restore_code`, `type`, `visible`)
        VALUES (2, '2015-04-01 13:50:00', 'test2', '".$passHash2."', 'test2@test.ru', '', 'user', 1)");

    $this->execute("INSERT INTO `{{user_profile}}` (`user_id`, `name`, `last_name`, `patronymic`, `address`, `phone`, `birthday`)
        VALUES (2, 'Петр', '', '', '', '', '0000-00-00');");

    $this->generateOrders();
	}

	public function down()
	{
    $this->delete('{{order_product_history}}', 'order_product_id IN ('.implode(', ', range(1, 66)).')');
    $this->delete('{{order_product}}', 'id IN ('.implode(', ', range(1, 66)).')');
    $this->delete('{{order}}', 'id IN ('.implode(', ', range(1, 34)).')');
    $this->delete('{{user_profile}}', 'user_id IN (1, 2)');
    $this->delete('{{user}}', 'id IN (1, 2)');

		return true;
	}

  private function generateOrders()
  {
    $data = array(
      array(
        'sum' => 14000,
        'date_create' => '2014-05-21 07:55:20',
        'delivery_price' => 0,
        'status_id' => 1,
      ),
      array(
        'sum' => 60000,
        'date_create' => '2014-05-21 07:44:30',
        'delivery_price' => 500,
        'status_id' => 2,
      ),
      array(
        'sum' => 1300,
        'date_create' => '2014-05-21 05:24:10',
        'delivery_price' => 2000,
        'status_id' => 3,
      ),
      array(
        'sum' => 400,
        'date_create' => '2014-05-21 07:14:20',
        'delivery_price' => 100,
        'status_id' => 4,
      ),
      array(
        'sum' => 100,
        'date_create' => '2014-05-21 15:10:40',
        'delivery_price' => 0,
        'status_id' => 5,
      ),
      array(
        'sum' => 0,
        'date_create' => '2014-05-21 21:14:04',
        'delivery_price' => 0,
        'status_id' => 1,
      )
    );

    for($i = 1; $i < 35; $i++)
    {
      $index = $i%5;
      $item = $data[$index];

      $this->insert('{{order}}', array(
        'id' => $i,
        'user_id' => 1,
        'name' => 'Василий',
        'sum' => $item['sum'],
        'date_create' => $item['date_create'],
        'status_id' => $item['status_id'],
      ));

      $this->insert('{{order_delivery}}', array('order_id' => $i, 'delivery_type_id' => 2, 'delivery_price' => $item['delivery_price']));

      $this->generateOrderProducts($item['sum'], $i);
    }
  }

  private function generateOrderProducts($sum, $orderId)
  {
    $names = array(
      'Отбойник  дверной Archie G003 A',
      'Фрамир Мальта',
      'Фрамир Фридрих',
      'Dariano Porte Рондо-3 Кортекс',
      'Петля стальная универсальная Morelli MS 100*70*2.5-4BB SG',
      'Ручка дверная Morelli MH-03 SG/GP',
      'Защёлка бесшумная под цилиндр Morelli 1885P PG',
      'Mario Rioli 204LF',
      'Ручка дверная Morelli MH-02 MAB',
      'Дерен белый',
      'Гортензия метельчатая',
      'Бересклет крылатый',
      'Dariano Porte Махаон',
      'Краснодеревщик 3343',
      'Mario Rioli 220',
      'Краснодеревщик 7300',
      'Dariano Porte Галактика'
    );

    $data = array(
      0 => array(
        array(
          'name' => $names[1],
          'price' => 0,
          'count' => 1,
          'sum' => 0,
        ),
        array(
          'name' => $names[4],
          'price' => 0,
          'count' => 5,
          'sum' => 0,
        ),
      ),
      100 => array(
        array(
          'name' => $names[4],
          'price' => 100,
          'count' => 1,
          'sum' => 100,
        ),
      ),
      400 => array(
        array(
          'name' => $names[5],
          'price' => 100,
          'count' => 4,
          'sum' => 400,
        ),
      ),
      1300 => array(
        array(
          'name' => $names[6],
          'price' => 200,
          'count' => 2,
          'sum' => 400,
        ),
        array(
          'name' => $names[7],
          'price' => 500,
          'count' => 1,
          'sum' => 500,
        ),
        array(
          'name' => $names[1],
          'price' => 100,
          'count' => 3,
          'sum' => 300,
        ),
      ),
      14000 => array(
        array(
          'name' => $names[2],
          'price' => 4000,
          'count' => 1,
          'sum' => 4000,
        ),
        array(
          'name' => $names[3],
          'price' => 1000,
          'count' => 3,
          'sum' => 3000,
        ),
        array(
          'name' => $names[1],
          'price' => 2000,
          'count' => 1,
          'sum' => 2000,
        ),
        array(
          'name' => $names[1],
          'price' => 4000,
          'count' => 1,
          'sum' => 4000,
        ),
      ),
      60000 => array(
        array(
          'name' => $names[2],
          'price' => 10000,
          'count' => 6,
          'sum' => 10000,
        ),
      ),
    );

    foreach($data[$sum] as $i => $item)
    {
      $id = self::$orderProductCount++;
      $this->insert('{{order_product}}', array(
        'id' => $id,
        'order_id' => $orderId,
        'name' => $item['name'],
        'price' => $item['price'],
        'count' => $item['count'],
        'sum' => $item['sum'],
      ));

      $this->insert('{{order_product_history}}', array(
        'order_product_id' => $id,
        'articul' => '00000'.$orderId,
      ));
    }
  }
}