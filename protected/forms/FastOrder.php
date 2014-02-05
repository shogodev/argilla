<?php
return array(
  'id' => Yii::app()->controller->basket->fastOrderFormId,
  'class' => 'form callback-form',
  'elements' => array(
   'phone' => array(
     'type' => 'text',
     'class' => 'inp one-click-phone',
   ),
   'name' => array('type' => 'text'),
  ),
  'buttons' => array(
    Yii::app()->controller->basket->submitFastOrderButton(
      'Купить в 1 клик',
      array('class' => 'btn red-btn')
    ),
  )
);