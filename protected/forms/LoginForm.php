<?php
return array(
  'class' => 'form registration-form m35',

  'layout' => "{title}\n{errors}\n{elements}{description}\n<div class=\"form-submit\">{buttons}</div>\n",

  'elements' => array(

    'login' => array(
      'id' => 'popup-login',
      'type' => 'text',
    ),

    'password' => array(
      'id' => 'popup-password',
      'type' => 'password',
    ),

    'rememberMe' => array(
      'id' => 'popup-remember',
      'label' => 'Запомнить меня',
      'type' => 'checkbox',
      'layout' => '<div class="nofloat form-hint"><div class="remember-me-box">{input}{label}</div><div class="right"><a href="'.Yii::app()->createUrl('user/restore').'">Забыли пароль?</a></div></div>'
    ),
  ),

  'buttons' => array(
    'submit' => array(
      'type'  => 'submit',
      'class' => 'btn red-contour-btn rounded-btn h34btn opensans s15 bb uppercase',
      'value' => 'Войти'
    ),
  ),
);