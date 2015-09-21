Для подключения редактируем frontend.php:
1) Добавить в импорт
  'import' => array(
  	...
	  'ext.eoauth.*',
		'ext.eoauth.lib.*',
		//'extensions.lightopenid.*',
		'ext.eauth.*',
		'ext.eauth.services.*',
  )

2) Добавляем компонент
  'components' => array(
		...
		'eauth' => array(
			'class' => 'ext.eauth.EAuth',
			'popup' => true,
			'cache' => false,
			'cacheExpire' => 0,
			'services' => require(__DIR__.'/oauth.php'),
		),
  )

3) Создаем конфиг

  Копируем файл из protected/extensions/eauth/oauth.php.sample в protected/config/oauth.php
  Задаем все параметры.

Официальная документация компонента:
https://github.com/Nodge/yii-eauth