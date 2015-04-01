<?php
return array(

  'class' => 'form basket-form m35',

  'elements' => array(

    'name' => array(
      'type' => 'text'
    ),

    'phone' => array(
      'type' => 'tel'
    ),

    'email' => array(
      'type' => 'text'
    ),

    'delivery_id' => array(
      'template' => '<div class="radio-input">{input} {label}</div>',
      'separator' => '',
      'type' => 'radiolist',
      'items' => CHtml::listData(OrderDeliveryType::model()->findAll(), 'id', 'name'),
    ),

    'address' => array(
      'type' => 'text'
    ),

    'payment_id' => array(
      'template' => '<div class="radio-input">{input} {label}</div>',
      'separator' => '',
      'type' => 'radiolist',
      'items' => CHtml::listData(OrderPaymentType::model()->findAll(), 'id', 'name'),
    ),

    'payment' => array(
      'type' => 'form',
      'layout' => '<div id="platron-block">{elements}</div>',
      'model' => new OrderPayment(),
      'elements' => array(
        'payment_type_id' => array(
          'template' => '<div class="radio-input">{input} {label}</div>',
          'separator' => '',
          'type' => 'radiolist',
          'items' => CHtml::listData(PlatronPaymentType::model()->findAll(), 'id', 'imageLabel'),
        ),
      )
    ),

    'comment' => array(
      'type' => 'textarea'
    ),
  ),

  'buttons' => array('submit' => array(
    'type' => 'button',
    'value' => 'Оформить заказ',
    'class' => 'right btn red-contour-btn rounded-btn h34btn opensans s15 bb uppercase',
  ))
);