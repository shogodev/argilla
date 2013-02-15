<?php
/**
 * User: tatarinov
 * Date: 17.10.12
 */
return array(
  'class' => 'form m20',
  'layout' => "{title}<div class=\"l-main\">\n{errors}\n{elements}\n<div class=\"text-container form-hint\">{description}</div>\n<div class=\"form-submit\">{buttons}</div>\n</div>\n",

  'elements' => array(

    'login' => array(
      'label'  => 'Логин',
      'type'   => 'text',
    ),

    'password' => array(
      'label'           => 'Пароль',
      'type'            => 'password',
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
      'class' => 'btn btn-red btn-submit',
      'value' => 'Войти'
    ),
  ),
);