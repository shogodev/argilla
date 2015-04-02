<?php

return array(
  'class' => 'form registration-form m35',

  'elements' => array(
    'name' => array('type' => 'text'),

    'address' => array('type' => 'text'),

    'phone' => array('type' => 'tel'),

    'birthday' => array(
      'type' => 'DateIntervalWidget',
      'form' => $this,
      'template' => '<span class="select-container form-size-third date-select">{day}</span>
                     <span class="select-container form-size-third date-select">{month}</span>
                     <span class="select-container form-size-third date-select">{year}</span>',
      'attribute' => 'birthday',
      'rangeYears' => array(intval(date("Y")) - 100, intval(date("Y")) - 5),
    ),

  ),

  'buttons' => array(
    'submit' => array(
      'type' => 'button',
      'value' => 'Сохранить',
      'class' => 'btn red-contour-btn rounded-btn h34btn opensans s15 bb uppercase'
    ),
  ),
);