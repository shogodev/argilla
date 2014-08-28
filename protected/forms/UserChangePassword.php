<?php
return array(
  'class' => 'form auth-form',

  'description' => '<div class="m30 form-hint">Поля, отмеченные знаком <span class="required">*</span>, обязательны для заполнения.</div>',

  'elementsLayout' => '<div class="form-row m30">{label}<div class="form-field">{input}{hint}{error}</div></div>',

  'elements' => array(
    'oldPassword' => array('type' => 'password'),

    'password' => array('type' => 'password'),

    'confirmPassword' => array('type' => 'password'),
  ),

  'buttons' => array(
    'submit' => array(
      'type'  => 'button',
      'value' => 'Сохранить',
      'class' => 'btn',
    ),
  ),
);