<?php
/**
 * @var User $model
 */
?>
Здравствуйте<?php echo !empty($model->user->name) ? ' '.$model->user->name : ''?><br />
<br />Вы изменили пароль входа на сайт <?php echo Yii::app()->params->project?>
<br /><br />
Ваш логин: <?php echo $model->login?><br/>
Ваш новый пароль: <?php echo $model->password?><br/>