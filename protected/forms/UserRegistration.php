<?php
return array(
  'description'     => 'Поля, отмеченные знаком <span class="required">*</span>, обязательны для заполнения.',

  'attributes' => array(
    'class' => 'form big-inputs m20',
    'enctype' => 'multipart/form-data',
  ),

  'ajaxSubmit' => false,

  'elements' => array(

    'login'            => array('type' => 'text'),
    'email'            => array('type' => 'text'),
    'password'         => array('type' => 'password'),
    'password_confirm' => array('type' => 'password'),

    'extendedData' => array(
      'type'     => 'form',
      'elements' => array(

        'last_name'        => array('type' => 'text'),
        'name'             => array('type' => 'text'),
        'patronymic'       => array('type' => 'text'),

        'address' => array(
          'type' => 'text',
          'class' => 'inp suboffice-address'
        ),

        '<div class="hr1"></div>',

        'birthday' => array(
          'type'         => 'DateIntervalWidget',
          'layout'       => '{input}',
          'attribute'    => 'birthday',
          'hideCalendar' => true,
          'rangeYears'   => array(intval(date("Y"))-100, intval(date("Y"))-5),
        ),

        'avatar' => array(
          'type'     => 'FMultiFileUpload',
          'baseType' => 'file',
        ),

        '<div class="hr1"></div>',
      )
    ),

    'verifyCode' => array(
      'type' => 'CaptchaWidget',
    ),

  ),

  'buttons' => array(
    'submit' => array(
      'type'  => 'submit',
      'value' => 'Отправить',
      'class' => 'btn btn-red btn-submit'
    ),
  ),
);