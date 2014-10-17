<?php
/**
 * @var User $model
 * @var UserProfile $profile
 */
?>
Здравствуйте<?php echo !empty($profile->name) ? ' '.$profile->name : ''?>!<br />
<br />Вы зарегистрировались на сайте <?php echo Yii::app()->params->project?>
<br /><br />
Ваш логин: <?php echo $model->login?><br/>
Ваш пароль: <?php echo $model->password?><br/>