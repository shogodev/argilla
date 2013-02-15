<?php
return array(

  'class' => 'form big-inputs m20',
  'description' => 'Поля, отмеченные знаком <span class="required">*</span> , обязательны для заполнения.',


  'elements' => array(

    'dealer_id' => array(
      'type' => 'hidden',
    ),

    'product_id' => array(
      'type' => 'dropdownlist',
      'items' => CHtml::listData(Product::model()->findAll(), 'id', 'name'),
    ),

    'size' => array(
      'type' => 'text'
    ),

    'color' => array(
      'type' => 'text'
    ),

    '<div class="hr1"></div>',

    'name' => array(
      'type' => 'text'
    ),

    'phone' => array(
      'type' => 'text'
    ),

    '<div class="hr1"></div>',

    'email' => array(
      'type' => 'text'
    ),

    'address' => array(
      'type' => 'textarea'
    ),

    'comment' => array(
      'type' => 'textarea'
    ),
  ),

  'buttons' => array('submit' => array(
    'type' => 'button',
    'value' => 'Отправить',
    'class' => 'btn btn-red btn-submit',
  ))
);