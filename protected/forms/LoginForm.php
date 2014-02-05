<?php
return array(
  'class' => 'form personal-form centered-form',
  'layout' => "{title}\n{errors}\n{elements}{description}\n<div class=\"form-submit\">{buttons}</div>\n",

  'elements' => array(

    'login' => array(
      'type' => 'text',
    ),

    'password' => array(
      'type' => 'password',
    ),

    'rememberMe' => array(
      'label'           => 'Запомнить меня',
      'type'            => 'checkbox',
      'defaultTemplate' => '<div class="nofloat form-hint">
                              <div class="remember-me-box">{input}{label}</div>
                              <div class="right"><a href="'.Yii::app()->controller->createUrl('user/restore').'">Забыли пароль?</a></div>
                            </div>'
    ),
  ),

  'buttons' => array(
    'submit' => array(
      'type'  => 'submit',
      'class' => 'btn red-btn wide-paddings-btn',
      'value' => 'Войти'
    ),
  ),
);