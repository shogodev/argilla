<?php
return array(
  'class' => 'form auth-form',
  'layout' => "{title}\n{elements}\n{description}\n<div class=\"form-submit input-level\">{buttons}</div>\n",

  'elements' => array(
    'email'            => array('type' => 'text'),
    'password'         => array('type' => 'password'),
    'password_confirm' => array('type' => 'password'),

    'extendedData' => array(
      'type'     => 'form',
      'elements' => array(
        'name' => array('type' => 'text'),
       )
    ),
  ),

  'buttons' => array(
    'submit' => array(
      'type' => 'htmlButton',
      'label' => 'Зарегистрироваться',
      'class' => 'btn orange-btn h36btn s19 bb'
    ),
  ),
);