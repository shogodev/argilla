Как подключить
=========================

1) Создаем таблицы выполнив команду миграции с параметрами:

  protected/yiic migrate up --migrationPath=frontend.extensions.cackle.migrations

2) Создаем конфиг

  Копируем файл из protected/extensions/cackle/cackle.php.sample в protected/config/cackle.php
  Задаем все параметры.

  Посмотреть можно:
  http://admin.cackle.me/site/(id проекта)/review/install
  Внизу вкладка "CMS Платформа"

3) Прописываем компонент в protected/config/frontend.php

    'components' => array(
      ....
      'cackle' => array(
        'class' => 'ext.cackle.Cackle',
      ),
      ....
    );

4) Регистрируем консольную команду в protected/config/console.php

    Добавляем путь к коммаде в $config['commandMap']

    $config['commandMap'] = array(
      ....
      'cacklesync' => array(
        'class' => 'frontend.extensions.cackle.CackleSyncCommand',
      ),
      ....
    );

    команда по новой импортирует все записи:
      protected/yiic cacklesync --mode=clear

    команды обновляют только измененные:
      protected/yiic cacklesync --mode=update
    или
      protected/yiic cacklesync

5) Прописываем команду обновления в cron
  0 4 */1 * * /usr/local/bin/php /usr/www/проект/html/protected/yiic cacklesync 1>/dev/null