<?php

class m010101_000004_feel_rbac extends CDbMigration
{
	public function up()
	{
      $this->execute("INSERT INTO `" . $this->dbConnection->tablePrefix . "auth_user` (`id`, `username`, `password`, `visible`) VALUES
                                                                                      (1, 'root', '123', 1),
                                                                                      (2, 'content', '123', 1),
                                                                                      (3, 'seo', '123', 1);");
      
      $this->execute("INSERT INTO `" . $this->dbConnection->tablePrefix . "auth_item` (`name`, `title`, `type`, `description`, `bizrule`, `data`) VALUES
                                                                                      ('admin', 'Администратор', 2, '', NULL, NULL),
                                                                                      ('banner:banner', 'Баннеры-Баннеры', 1, NULL, NULL, NULL),
                                                                                      ('banner:banner:index', 'Баннеры-Баннеры-Главная', 0, NULL, NULL, NULL),
                                                                                      ('contact:contact', 'Контакты-Контакты', 1, NULL, NULL, NULL),
                                                                                      ('contact:contact:update', 'Контакты-Контакты-Обновление', 0, NULL, NULL, NULL),
                                                                                      ('content', 'Контент', 2, '', NULL, NULL),
                                                                                      ('info:info', 'Информация', 1, '', NULL, NULL),
                                                                                      ('info:info:index', 'Информация-Главная', 0, '', NULL, NULL),
                                                                                      ('links:links', 'Каталог ссылок-Каталог ссылок', 1, NULL, NULL, NULL),
                                                                                      ('links:links:index', 'Каталог ссылок-Каталог ссылок-Главная', 0, NULL, NULL, NULL),
                                                                                      ('news:news', 'Новости-Новости', 1, NULL, NULL, NULL),
                                                                                      ('news:news:index', 'Новости-Новости-Главная', 0, NULL, NULL, NULL),
                                                                                      ('news:newsSection', 'Новости-Разделы', 1, NULL, NULL, NULL),
                                                                                      ('news:newsSection:update', 'Новости-Разделы-Обновление', 0, NULL, NULL, NULL),
                                                                                      ('notification:email', 'Уведомления-E-mails', 1, NULL, NULL, NULL),
                                                                                      ('notification:email:index', 'Уведомления-E-mails-Главная', 0, NULL, NULL, NULL),
                                                                                      ('order:order', 'Заказы-Заказы', 1, NULL, NULL, NULL),
                                                                                      ('order:order:update', 'Заказы-Заказы-Обновление', 0, NULL, NULL, NULL),
                                                                                      ('product:product', 'Каталог продукции-Продукты', 1, NULL, NULL, NULL),
                                                                                      ('product:product:update', 'Каталог продукции-Продукты-Обновление', 0, NULL, NULL, NULL),
                                                                                      ('product:productCategory', 'Каталог продукции-Бренды', 1, NULL, NULL, NULL),
                                                                                      ('product:productCategory:update', 'Каталог продукции-Бренды-Обновление', 0, NULL, NULL, NULL),
                                                                                      ('product:productParamName', 'Каталог продукции-Параметры', 1, NULL, NULL, NULL),
                                                                                      ('product:productParamName:index', 'Каталог продукции-Параметры-Главная', 0, NULL, NULL, NULL),
                                                                                      ('rbac:role', 'Доступ-Роли', 1, NULL, NULL, NULL),
                                                                                      ('rbac:role:index', 'Доступ-Роли-Главная', 0, NULL, NULL, NULL),
                                                                                      ('rbac:user', 'Доступ-Пользователи', 1, NULL, NULL, NULL),
                                                                                      ('rbac:user:index', 'Доступ-Пользователи-Главная', 0, NULL, NULL, NULL),
                                                                                      ('seo', 'SEO', 2, '', NULL, NULL),
                                                                                      ('seo:meta', 'Мета тэги-Мета тэги', 1, NULL, NULL, NULL),
                                                                                      ('seo:meta:index', 'Мета тэги-Мета тэги-Главная', 0, NULL, NULL, NULL),
                                                                                      ('seo:metaByMask', 'Мета теги-Маска', 1, NULL, NULL, NULL),
                                                                                      ('seo:metaByMask:update', 'Мета теги-Маска-Обновление', 0, NULL, NULL, NULL),
                                                                                      ('seo:metaByUrlManager', 'Мета теги-Шаблоны', 1, NULL, NULL, NULL),
                                                                                      ('seo:metaByUrlManager:index', 'Мета теги-Шаблоны-Главная', 0, NULL, NULL, NULL),
                                                                                      ('seo:metaRoute', 'Мета теги-Шаблоны пути', 1, NULL, NULL, NULL),
                                                                                      ('seo:metaRoute:index', 'Мета теги-Шаблоны пути-Главная', 0, NULL, NULL, NULL),
                                                                                      ('seo:metaUrlManager', 'Мета теги-Шаблоны', 1, NULL, NULL, NULL),
                                                                                      ('seo:metaUrlManager:index', 'Мета теги-Шаблоны-Главная', 0, NULL, NULL, NULL),
                                                                                      ('tag:tag', 'Тэги-Тэги', 1, NULL, NULL, NULL),
                                                                                      ('tag:tag:index', 'Тэги-Тэги-Главная', 0, NULL, NULL, NULL);");
      
      $this->execute("INSERT INTO `" . $this->dbConnection->tablePrefix . "auth_item_child` (`parent`, `child`) VALUES
                                                                                            ('admin', 'banner:banner'),
                                                                                            ('content', 'banner:banner'),
                                                                                            ('banner:banner', 'banner:banner:index'),
                                                                                            ('admin', 'contact:contact'),
                                                                                            ('content', 'contact:contact'),
                                                                                            ('contact:contact', 'contact:contact:update'),
                                                                                            ('admin', 'info:info'),
                                                                                            ('content', 'info:info'),
                                                                                            ('info:info', 'info:info:index'),
                                                                                            ('admin', 'links:links'),
                                                                                            ('seo', 'links:links'),
                                                                                            ('links:links', 'links:links:index'),
                                                                                            ('admin', 'news:news'),
                                                                                            ('content', 'news:news'),
                                                                                            ('news:news', 'news:news:index'),
                                                                                            ('admin', 'news:newsSection'),
                                                                                            ('news:newsSection', 'news:newsSection:update'),
                                                                                            ('admin', 'notification:email'),
                                                                                            ('notification:email', 'notification:email:index'),
                                                                                            ('admin', 'order:order'),
                                                                                            ('order:order', 'order:order:update'),
                                                                                            ('admin', 'product:product'),
                                                                                            ('content', 'product:product'),
                                                                                            ('product:product', 'product:product:update'),
                                                                                            ('admin', 'product:productCategory'),
                                                                                            ('content', 'product:productCategory'),
                                                                                            ('product:productCategory', 'product:productCategory:update'),
                                                                                            ('admin', 'product:productParamName'),
                                                                                            ('content', 'product:productParamName'),
                                                                                            ('product:productParamName', 'product:productParamName:index'),
                                                                                            ('rbac:role', 'rbac:role:index'),
                                                                                            ('admin', 'rbac:user'),
                                                                                            ('rbac:user', 'rbac:user:index'),
                                                                                            ('admin', 'seo:meta'),
                                                                                            ('seo', 'seo:meta'),
                                                                                            ('seo:meta', 'seo:meta:index'),
                                                                                            ('admin', 'seo:metaByMask'),
                                                                                            ('seo', 'seo:metaByMask'),
                                                                                            ('seo:metaByMask', 'seo:metaByMask:update'),
                                                                                            ('admin', 'seo:metaByUrlManager'),
                                                                                            ('seo', 'seo:metaByUrlManager'),
                                                                                            ('seo:metaByUrlManager', 'seo:metaByUrlManager:index'),
                                                                                            ('admin', 'seo:metaRoute'),
                                                                                            ('seo', 'seo:metaRoute'),
                                                                                            ('seo:metaRoute', 'seo:metaRoute:index'),
                                                                                            ('admin', 'seo:metaUrlManager'),
                                                                                            ('seo', 'seo:metaUrlManager'),
                                                                                            ('seo:metaUrlManager', 'seo:metaUrlManager:index'),
                                                                                            ('admin', 'tag:tag'),
                                                                                            ('content', 'tag:tag'),
                                                                                            ('tag:tag', 'tag:tag:index');");
      
      $this->execute("INSERT INTO `" . $this->dbConnection->tablePrefix . "auth_assignment` (`itemname`, `userid`, `bizrule`, `data`) VALUES
                                                                                            ('admin', '1', NULL, 'N;'),
                                                                                            ('content', '2', NULL, 'N;'),
                                                                                            ('seo', '3', NULL, 'N;');
                                                                                            ");
      
	}

	public function down()
	{
		echo "m010101_000004_fee_rbac does not support migration down.\n";
		return false;
	}
}