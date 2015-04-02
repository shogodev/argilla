<?php
return array(
  'class' => 'form registration-form m35',

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
      'class' => 'btn red-contour-btn rounded-btn h34btn opensans s15 bb uppercase'
    ),
  ),
);