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
 * @var User $model
 */
?>
<div style="font-size: 24px; font-weight: bold; text-align: center; margin-bottom: 30px">
  ВАШ ПАРОЛЬ ИЗМЕНЕН!
</div>

<div style="margin-bottom: 20px">
  <div style="font-size: 24px">Здравствуйте<?php echo !empty($model->name) ? ', '.$model->name : ''; ?>!</div>
  Вы изменили пароль входа на сайт <a href="<?php echo $host?>" target="_blank" style="color: #f88101"><?php echo $project?></a>!<br/>
  Ваш логин: <?php echo $model->login?><br/>
  Ваш новый пароль: <?php echo $model->password?><br/>
</div>