<?php
return array(
  'class' => 'form profile-form',

  'elements' => array(
    'login' => array('type' => 'text'),

    'email' => array('type' => 'text'),

    'password' => array('type' => 'password'),

    'confirmPassword' => array(
      'type' => 'password',
      'layout' => '<div class="form-row m12 confirmation-label">{label}<div class="form-field">{input}{error}</div></div>'
    ),

    'profile' => array(
      'type' => 'form',
      'layout' => "{title}\n{elements}\n{description}\n{buttons}\n",
      'elements' => array(

        'name' => array('type' => 'text'),

        'address' => array('type' => 'text'),

        'phone' => array('type' => 'tel'),

        'birthday' => array(
          'type' => 'DateIntervalWidget',
          'form' => $this['profile'],
          'template' => '<span class="select-container form-size-third date-select">{day}</span>
                     <span class="select-container form-size-third date-select">{month}</span>
                     <span class="select-container form-size-third date-select">{year}</span>',
          'attribute' => 'birthday',
          'rangeYears' => array(intval(date("Y")) - 100, intval(date("Y")) - 5),
        ),

        'subscribe' => array('type' => 'checkbox'),
      )
    ),
  ),

  'buttons' => array(
    'submit' => array(
      'type' => 'button',
      'value' => 'Сохранить',
      'class' => 'btn green-contour-btn h46btn p20btn r11 s23 uppercase'
    ),
  ),
);