<?php
return array(
  'class' => 'form auth-form',

  'elements' => array(
    'login' => array('type' => 'text'),

    'email' => array('type' => 'text'),

    'password' => array('type' => 'password'),

    'confirmPassword' => array('type' => 'password'),

    'profile' => array(
      'type' => 'form',
      'layout' => "{title}\n{elements}\n{description}\n{buttons}\n",
      'elements' => array(
        'name' => array('type' => 'text'),
       )
    ),
  ),

  'buttons' => array(
    'submit' => array(
      'type' => 'button',
      'label' => 'Зарегистрироваться',
      'class' => 'btn'
    ),
  ),
);