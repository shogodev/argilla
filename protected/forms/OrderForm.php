<?php
return array(

  'class' => 'form big-inputs m20',
  'description' => 'Поля, отмеченные знаком <span class="required">*</span> , обязательны для заполнения.',


  'elements' => array(

    'name' => array(
      'type' => 'text'
    ),

    'phone' => array(
      'type' => 'text'
    ),

    'email' => array(
      'type' => 'text'
    ),

    'payment_id' => array(
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

    'address' => array(
      'type' => 'textarea'
    ),

    'comment' => array(
      'type' => 'textarea'
    ),
  ),

  'buttons' => array('submit' => array(
    'type' => 'button',
    'value' => 'Отправить',
    'class' => 'btn btn-red btn-submit',
  ))
);