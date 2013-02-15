Здравствуйте<?php echo !empty($model->user->name) ? ' '.$model->user->name : ''?>!<br />
<br />Для продолжения процедуры восстановления вам необходимо перейди по ссылке <a href="<?php echo $restoreUrl?>"><?php echo $restoreUrl?></a>
<br /><br />-----------------------------------------------<br /><br />
С уважением, <?php echo Yii::app()->params->project?><br />
web: <a href="<?php echo Yii::app()->request->hostInfo; ?>"><?php echo Yii::app()->request->hostInfo; ?></a>