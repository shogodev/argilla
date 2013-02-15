<?php
$a = 1;

return array(
  'class' => 'form',
  'description' => '<span class="required">*</span> - поля, обязательные для заполнения',

  'ajaxSubmit' => false,
  'attributes' => array(
    'enctype' => 'multipart/form-data',
  ),

  'elements' => array(
    'name' => array('type' => 'text'),

    'file' => array(
      'type' => 'FMultiFileUpload',
      'form' => $this,
    ),

    'phone' => array('type' => 'text'),

    'content' => array(
      'type' => 'textarea',
      'attributes' => array(
        'cols' => 5,
        'rows' => 5
      ),
    ),
  ),

  'buttons' => array(
    'submit' => array(
      'type' => 'image',
      'src' => 'i/btn_send.png',
    )
  ),
);