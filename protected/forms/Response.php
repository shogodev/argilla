<?php
return array(
  'class' => 'form',
  'description' => 'Поля, отмеченные знаком <span class="required">*</span> , обязательны для заполнения.',

  'elements' => array(
    'product_id' => array('type' => 'hidden'),
    'name' => array('type' => 'text'),
    'email' => array('type' => 'text'),
    'content' => array('type' => 'textarea')
  ),

  'buttons' => array(
    'submit' => array(
      'type' => 'image',
      'src' => 'i/confirm_btn.png'
    )
  ),
);