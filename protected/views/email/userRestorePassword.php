<?php
/**
 * @var RestorePassword $model
 * @var string $password
 */
?>
Здравствуйте<?php echo !empty($model->user->profile->name) ? ' '.$model->user->profile->name : ''?>!<br />
<br />Восстановлен пароль входа на сайт <?php echo Yii::app()->params->project?>
<br /><br />
Ваш логин: <?php echo $model->user->login?><br/>
Ваш новый пароль: <?php echo $password?><br/>