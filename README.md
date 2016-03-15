#Argilla [![Build Status](https://travis-ci.org/shogodev/argilla.svg?branch=master)](https://travis-ci.org/shogodev/argilla) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/shogodev/argilla/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/shogodev/argilla/?branch=master) [![Code Coverage](https://scrutinizer-ci.com/g/shogodev/argilla/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/shogodev/argilla/?branch=master)
##Что это?

[![Join the chat at https://gitter.im/shogodev/argilla](https://badges.gitter.im/shogodev/argilla.svg)](https://gitter.im/shogodev/argilla?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)
Argilla это CMF, реализованная на [Yii Framework](http://www.yiiframework.com/). Основное назначение - быстрое разворачивание сайтов высокой сложности. Написана в компании [Shogo](http://shogo.ru). Лучше всего подходит для нашей специализации - branding & retailing. То есть для крутых брендовых сайтов и сложных интернет-магазинов.
##Технические требования
Обязательно:

  * Unix (not well tested on windoze)
  * PHP 5.4
  * Mysql 5.1+
  * [Phing](http://www.phing.info/)
  * Yii Framework (для trunk всегда последняя стабильная версия 1.1.x)

Рекомендуется:

  * Java в path для компиляции js
  * mysql, mysqldump в path для удобной работы с бд
  * Apache (для .htaccess)

##Features

  * Два независимых приложения с одним конфигом БД - frontend и backend. Frontend может жить без backend.
  * Frontend - стандартное приложение Yii. Мы добавили только крутую работу с формами.
  * Backend - модульная архитекутра, красивый CRUD доступен из коробки. Сделать простой модуль - дело 5 минут.
  * Backend modules:
    * RBAC
    * SEO - удобное формирование тайтлов и мета-тегов, редиректы, подмены урлов
    * Simple banner system
    * Пользовательские комментарии для любой модели
    * Info-страницы - наборы текстовых страниц произвольной структуры
    * Menu - управление меню из бекенда
    * Product, order - модули для интернет-магазина со сложной архитектурой. Два вида параметров - свободные (EAV), и прописываемые в модели.
    * Текстовые блоки
  * Сборка через phing (still in development)
    * Работа с дампами БД, автоматическое создание схем БД. Не паримся по поводу triggers, views и routines
    * Рутинные операции - почистить кеш, скомпилировать js, выставить правильные права
    * PHPUnit
    * CodeSniffer, Mess Detector, Copy-Paste detector
    * Готово к continuous integration

#Installation

	git clone git://github.com/shogodev/argilla.git webroot/
	cd webroot
	cp protected/config/db.php.sample protected/config/db.php
	vim protected/config/db.php
	phing applySchema
	./protected/yiic migrate
	./protected/yiic rbac build

##Demo content

Для заполнения базы демонстрационным контентом в директории с проектом (webroot) нужно выполнить команду:

    protected/yiic migrate up --migrationPath=frontend.migrations.demo

Удаление демонстрационного контента(если миграций больше не применялись):

    protected/yiic migrate down 8 --migrationPath=frontend.migrations.demo

