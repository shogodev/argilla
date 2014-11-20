<?php
return array(

  'class' => 'form order-form m40',

  'description' => '<div class="form-hint m30">Поля, отмеченные знаком <span class="required">*</span> , обязательны для заполнения.</div>',

  'elementsLayout' => '<div class="form-row m15">{label}<div class="form-field">{input}{error}</div></div>',

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
      'template' => '<div class="m5">{input} {label}</div>',
      'separator' => '',
      'type' => 'radiolist',
      'items' => CHtml::listData(OrderDeliveryType::model()->findAll(), 'id', 'name'),
    ),

    'address' => array(
      'type' => 'text'
    ),

    'payment_id' => array(
      'template' => '<div class="m5">{input} {label}</div>',
      'separator' => '',
      'type' => 'radiolist',
      'items' => CHtml::listData(OrderPaymentType::model()->findAll(), 'id', 'name'),
    ),

    'payment' => array(
      'type' => 'form',
      'model' => new OrderPayment(),
      'elements' => array(
        '<div class="platron_payment">Оплата через систему Platron</div>',
        'payment_type_id' => array(
          'type' => 'radiolist',
          'items' => CHtml::listData(PlatronPaymentType::model()->findAll(), 'id', 'label'),
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
    'class' => 'btn orange-btn h42btn s24 uppercase',
  ))
);