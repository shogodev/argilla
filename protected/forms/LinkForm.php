<?php
/**
 * @author Nikita Melnikov <nickswdit@gmail.com>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 */
return array(
	'class' => 'form m20',

	'elements' => array(

		'title' => array(
			'label'  => 'Текст ссылки',
			'type'   => 'text',
		),

		'email' => array(
			'label'  => 'Email',
			'type'   => 'text',
		),

		'url' => array(
			'label'  => 'Url',
			'type'   => 'text',
		),

		'product_id' => array(
			'type' => 'dropdownlist',
			'items' => CHtml::listData(LinkSection::model()->findAll(), 'id', 'name'),
		),

		'content' => array(
			'label'  => 'Полный текст',
			'type'   => 'textarea',
			'attributes' => array(
				'cols' => 5,
				'rows' => 5
			),
		),
	),

	'buttons' => array(
		'submit' => array(
			'type'  => 'submit',
			'class' => 'btn btn-red btn-submit',
			'value' => 'Отправить'
		),
	),
);