<?php
return array(
  'class' => 'form form-popup',
  'description' => 'Поля <span class="required">*</span> - обязательны для заполнения.',

  'elements' => array(
    'name' => array('type' => 'text'),
    'phone' => array('type' => 'text'),
    'time' => array('type' => 'text'),

    'content' => array(
      'type' => 'textarea',
      'attributes' => array(
        'cols' => 5,
        'rows' => 5
      )
    ),
  ),

  'buttons' => array(
    'submit' => array(
      'type' => 'image',
      'src' => 'i/btn_send.png'
    )
  ),
);