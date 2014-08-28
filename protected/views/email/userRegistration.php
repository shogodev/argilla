<?php
/**
 * @var User $model
 * @var UserProfile $userData
 * @var string $password
 */
?>
Здравствуйте<?php echo !empty($userData->name) ? ' '.$userData->name : ''?>!<br />
<br />Вы зарегистрировались на сайте <?php echo Yii::app()->params->project?>
<br /><br />
Ваш логин: <?php echo $model->login?><br/>
Ваш пароль: <?php echo $password?><br/>