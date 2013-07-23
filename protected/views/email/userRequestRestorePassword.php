<?php
/**
 * @var User $model
 */
?>
Здравствуйте<?php echo !empty($model->user->name) ? ' '.$model->user->name : ''?>!<br />
<br />Для продолжения процедуры восстановления вам необходимо перейди по ссылке <a href="<?php echo $restoreUrl?>"><?php echo $restoreUrl?></a>