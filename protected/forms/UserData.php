<?php

return array(
  'class' => 'form profile-form',

  'elementsLayout' => '<div class="form-row m20">{label}<div class="form-field">{input}{error}</div></div>',

  'elements' => array(
    'name' => array(
      'type' => 'text',
      'class' => 'inp'
    ),

    'address' => array(
      'type' => 'text',
      'class' => 'inp'
    ),

    'phone' => array(
      'type' => 'tel',
      'class' => 'inp'
    ),

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
      'class' => 'btn green-btn h47btn s30 bb'
    ),
  ),
);