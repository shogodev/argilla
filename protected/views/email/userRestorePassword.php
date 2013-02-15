Здравствуйте<?php echo !empty($model->user->name) ? ' '.$model->user->name : ''?>!<br />
<br />Восстановлен пароль ввхода на сайт <?php echo Yii::app()->params->project?>
<br /><br />
Ваш логин: <?php echo $model->login?><br/>
Ваш новый пароль: <?php echo $password?><br/>
<br /><br />-----------------------------------------------<br /><br />
С уважением, <?php echo Yii::app()->params->project?><br />
web: <a href="<?php echo Yii::app()->request->hostInfo; ?>"><?php echo Yii::app()->request->hostInfo; ?></a>