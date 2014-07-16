<?php

return array(
  'class' => 'form auth-form',

  'elements' => array(
    'name' => array('type' => 'text'),

    'address' => array('type' => 'text'),

    'phone' => array('type' => 'tel', 'class' => 'inp tel-inp'),
  ),

  'buttons' => array(
    'submit' => array(
      'type' => 'button',
      'value' => 'Сохранить',
      'class' => 'btn'
    ),
  ),
);