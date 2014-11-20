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
  РЕГИСТРАЦИЯ НА САЙТЕ <?php echo strtoupper($project)?>
</div>

<div style="margin-bottom: 20px">
  <div style="font-size: 24px">Здравствуйте<?php echo !empty($model->name) ? ', '.$model->name : ''; ?>!</div><br />
  Вы зарегистрировались на сайте <?php echo $project?><br />
  Ваш логин: <?php echo $model->login?><br/>
  Ваш пароль: <?php echo $model->password?><br/>
</div>