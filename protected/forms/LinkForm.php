<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 */
return array(
  'class' => 'form standard-form',

  //'description' => 'Поля, отмеченные знаком <span class="required">*</span> , обязательны для заполнения.',

  'elements' => array(

    'url' => array(
      'type' => 'text',
    ),

    'title' => array(
      'type' => 'text',
    ),

    'content' => array(
      'type' => 'textarea',
    ),

    'email' => array(
      'type' => 'text',
    ),

    'section_id' => array(
      'type' => 'dropdownlist',
      'items' => CHtml::listData(LinkSection::model()->findAll(), 'id', 'name'),
    ),
  ),

  'buttons' => array(
    'submit' => array(
      'type' => 'submit',
      'class' => 'btn sky-blue-btn h30btn p20btn s16 bb uppercase',
      'value' => 'Отправить'
    ),
  ),
);