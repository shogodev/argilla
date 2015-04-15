<?php
return array(
  'class' => 'form auth-form',

  'elements' => array(

    'login' => array(
      'type'   => 'text',
    ),

    'password' => array(
      'type' => 'password',
    ),

    'rememberMe' => array(
      'id' => 'popup_remember_me',
      'label' => 'Запомнить меня',
      'type' => 'checkbox',
      'layout' => '<div class="form-row m20">
          <div class="form-field">
            <a href="'.Yii::app()->createUrl('user/restore').'" class="fr s14">Забыли пароль?</a>
            <div class="remember-me-block">{input} {label}</div>
          </div>
        </div>'
    ),
  ),

  'buttons' => array(
    'submit' => array(
      'type'  => 'submit',
      'class' => 'btn red-contour-btn rounded-btn h34btn opensans s15 bb uppercase',
      'value' => 'Войти',
    ),
    "<a href=\"".Yii::app()->createUrl('user/registration')."\" class=\"fr btn red-btn new-user-btn\">Новый пользователь</a>"
  ),
);