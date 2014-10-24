<?php
return array(
  'id' => Yii::app()->controller->basket->fastOrderFormId,

  'class' => 'form fast-order-form',

  'elementsLayout' => '<div class="form-row m10">{label}<div class="form-field">{input}{error}</div>{hint}</div>',


  'elements' => array(
   'name' => array('type' => 'text'),

   'phone' => array('type' => 'tel'),

   'email' => array('type' => 'text'),

    '<div class="form-hint m10 center">Отправьте заказ и оператор подтвердит его<br />(смс или звонком на Ваш контактный номер телефона)</div>'
  ),
  'buttons' => array(
    Yii::app()->controller->basket->buttonSubmitFastOrder(
      'Оформить заказ',
      array('class' => 'btn green-btn h47btn s15 bb uppercase')
    ),


  )
);