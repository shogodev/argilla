<?php
return array(
  'type' => 'form',

  'layout' => "{elements}\n",

  'elements' => array(

    'delivery_type_id' => array(
      'type' => 'dropdownlist',
      'items' => CHtml::listData(OrderDeliveryType::model()->findAll(), 'id', 'name'),
    ),

    'address' => array(
      'type' => 'text',
      'required' => true
    ),

/*    'rememberAddress' => array(
      'label' => Yii::t('template', 'Адрес доставки'),
      'type' => 'dropdownlist',
    ),

    'metro_id' => array(
      'type' => 'dropdownlist',
      'required' => true,
    ),

    'street' => array(
      'label' => Yii::t('template', 'Улица'),
      'type' => 'text',
      'required' => true,
    ),

    'house' => array(
      'label' => Yii::t('template', 'Дом'),
      'type' => 'text',
      'required' => true,
      'layout' => FormLayouts::ELEMENT_LEFT
    ),

    'case' => array(
      'label' => Yii::t('template', 'Корпус'),
      'type' => 'text',
      'layout' => FormLayouts::ELEMENT_RIGHT
    ),

    'building' => array(
      'label' => Yii::t('template', 'Строение'),
      'type' => 'text',
      'layout' => FormLayouts::ELEMENT_LEFT
    ),

    'porch' => array(
      'label' => Yii::t('template', 'Подъезд'),
      'type' => 'text',
      'layout' => FormLayouts::ELEMENT_RIGHT
    ),

    'room' => array(
      'label' => Yii::t('template', 'Квартира/офис'),
      'type' => 'text',
      'layout' => FormLayouts::ELEMENT_LEFT
    ),

    'doorphone' => array(
      'label' => Yii::t('template', 'Домофон'),
      'type' => 'text',
      'layout' => FormLayouts::ELEMENT_RIGHT
    ),*/
  ),
);