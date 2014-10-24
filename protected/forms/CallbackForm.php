<?php
return array(
  'class' => 'form callback-form',

  'elements' => array(
    'phone' => array('type' => 'tel'),
    'name' => array('type' => 'text'),
  ),

  'buttons' => array(
    'submit' => array(
      'type' => 'button',
      'class' => 'btn red-btn',
      'value' => 'Заказать звонок'
    )
  ),
);