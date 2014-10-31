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
 * @var string $password
 */
?>
<div style="font-size: 24px; font-weight: bold; text-align: center; margin-bottom: 30px">
  ВОССТАНОВЛЕНИЕ ПАРОЛЯ НА САЙТЕ <?php echo strtoupper($project)?>
</div>

<div style="margin-bottom: 20px">
  <div style="font-size: 24px">Здравствуйте<?php echo !empty($model->name) ? ', '.$model->name : ''; ?>!</div><br />
  Для Вас был создан новый пароль. Вы можете его смнить на сайте <a href="<?php $this->createUrl('userProfile/data')?>" target="_blank"><?php echo strtoupper($project)?></a><br/>
  Ваш логин: <?php echo $model->user->login?><br/>
  Ваш новый пароль: <?php echo $password?><br/>
</div>