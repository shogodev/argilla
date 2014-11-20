<?php
/**
 * @var CController $this
 *
 * @var Email $sender
 * @var string $subject
 * @var string $host
 * @var string $project
 * @var string $content
 * @var ContactField[] $emails
 * @var ContactField $email
 * @var ContactField[] $phones
 * @var ContactField $phone
 *
 * @var RestorePassword $model
 */
?>
<div style="font-size: 24px; font-weight: bold; text-align: center; margin-bottom: 30px">
  ВОССТАНОВЛЕНИЕ ПАРОЛЯ НА САЙТЕ <?php echo strtoupper($project)?>
</div>

<div style="margin-bottom: 20px">
  <div style="font-size: 24px">Здравствуйте<?php echo !empty($model->name) ? ', '.$model->name : ''; ?>!</div><br />
  Для продолжения процедуры восстановления вам необходимо перейди по ссылке <a href="<?php echo $model->getRestoreUrl()?>"><?php echo $model->getRestoreUrl()?></a>
</div>