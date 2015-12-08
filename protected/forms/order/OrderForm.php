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

    FOrderForm::PAYMENT_FORM => require Yii::getPathOfAlias(FOrderForm::$DEFAULT_FORMS_PATH.'OrderPaymentForm').'.php',

    FOrderForm::DELIVERY_FORM => require Yii::getPathOfAlias(FOrderForm::$DEFAULT_FORMS_PATH.'OrderDeliveryForm').'.php',

    'comment' => array(
      'type' => 'textarea'
    ),
  ),

  'buttons' => array('submit' => array(
    'type' => 'htmlButton',
    'label' => 'Оформить заказ',
    'class' => 'js-order-submit-button fr btn red-btn btn-order-icon h45btn second-step-btn r11 s23 light uppercase',
  ))
);