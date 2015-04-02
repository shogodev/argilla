<?php
return array(
  'class' => 'form registration-form m35',

  'elements' => array(
    'oldPassword' => array('type' => 'password'),

    'password' => array('type' => 'password'),

    'confirmPassword' => array('type' => 'password'),
  ),

  'buttons' => array(
    'submit' => array(
      'type'  => 'button',
      'value' => 'Сохранить',
      'class' => 'btn red-contour-btn rounded-btn h34btn opensans s15 bb uppercase',
    ),
  ),
);