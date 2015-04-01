<?php
return array(
  'id' => Yii::app()->controller->basket->fastOrderFormId,

  'class' => 'one-click-form fr',

  'elementsLayout' => '<div class="form-row full-size-row m10"><div class="form-label s13">{label}</div><div class="form-field">{input}{error}</div>{hint}</div>',

  'elements' => array(
   'name' => array('type' => 'text'),

   'phone' => array('type' => 'tel', 'class' => 'inp tel-inp autofocus-inp'),

   'email' => array('type' => 'text'),

    //'<div class="form-hint m10 center">Отправьте заказ и оператор подтвердит его<br />(смс или звонком на Ваш контактный номер телефона)</div>'
  ),
  'buttons' => array(
    Yii::app()->controller->basket->buttonSubmitFastOrder(
      'Оформить заказ',
      array('class' => 'btn transparent-btn h38-btn')
    ),


  )
);