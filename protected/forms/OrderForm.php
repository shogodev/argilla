<?php
return array(

  'class' => 'form basket-form',

  'description' => '<div class="form-hint m30">Поля, отмеченные знаком <span class="required">*</span> , обязательны для заполнения.</div>',

  'elementsLayout' => '<div class="form-row m15">{label}<div class="form-field">{input}{error}</div></div>',

  'elements' => array(

    '<fieldset><div class="h2">Личные данные</div>',

    'name' => array(
      'type' => 'text'
    ),

    'phone' => array(
      'type' => 'tel'
    ),

    'email' => array(
      'type' => 'text'
    ),

    '</fieldset><fieldset><div class="h2">Доставка и оплата</div>',

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

    '   <div class="selfdelivery-block" style="display: none">
          <div class="h2">Как к нам проехать?</div>
          <div class="m20">
            <img src="i/scheme.png" alt="" />

          </div>
          <div class="center">
            <div class="m20">
              Вы можете самостоятельно забрать ваш заказ по адресу:<br />
              г. Москва, ул. Братиславская, д. 5, т. +7 (495) 726-00-62
            </div>
            Режим работы<br />
            с понедельника по пятницу с 10-00 до 18-00;<br />
            в субботу и воскресенье - выходной.
          </div>
        </div>
      </fieldset>'
  ),

  'buttons' => array('submit' => array(
    'type' => 'button',
    'value' => 'Оформить заказ',
    'class' => 'btn sky-blue-btn h30btn p20btn s16 bb uppercase',
  ))
);