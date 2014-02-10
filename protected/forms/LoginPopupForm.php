<?php
return array(
  'class' => 'form authorization-form',

  'elements' => array(

    'login' => array(
      'type'   => 'text',
    ),

    'password' => array(
      'type' => 'password',
    ),

    'rememberMe' => array(
      'id'              => 'popup_remember_me',
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
      'class' => 'fl btn red-btn enter-btn',
      'value' => 'Войти',
    ),
    "<a href=\"".Yii::app()->controller->createUrl('user/registration')."\" class=\"fr btn red-btn new-user-btn\">Новый пользователь</a>"
  ),
);