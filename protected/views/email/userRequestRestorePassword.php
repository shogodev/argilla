<?php
/**
 * @var RestorePassword $model
 */
?>
Здравствуйте<?php echo !empty($model->user->profile->name) ? ' '.$model->user->profile->name : ''?>!<br />
<br />Для продолжения процедуры восстановления вам необходимо перейди по ссылке <a href="<?php echo $model->getRestoreUrl()?>"><?php echo $model->getRestoreUrl()?></a>