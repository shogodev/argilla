<?php
return array(
  'type' => 'form',

  'layout' => "{elements}\n",

  'elements' => array(

    'payment_type_id' => array(
      'template' => '<div class="payment-input text-label">{input}{label}</div>',
      'separator' => '',
      'type' => 'radiolist',
      'items' => CHtml::listData(OrderPaymentType::model()->findAll(), 'id', 'name'),
      'class' => 'hidden'
    ),


    'system_payment_type_id' => array(
      'template' => '<div class="payment-input">{input}{label}</div> ',
      'class' => 'hidden',
      'separator' => '',
      'type' => 'radiolist',
      'items' => CHtml::listData(PlatronPaymentType::model()->findAll(), 'id', 'image'),
    ),
  )
);