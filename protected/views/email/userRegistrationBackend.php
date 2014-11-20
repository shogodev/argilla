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
<?php
$fields = array(
  'ID' => $model->id,
  'Логин' => $model->login,
  'E-mail' => $model->email
);
?>

<?php $this->renderPartial('frontend.views.email.defaultBackend', CMap::mergeArray($_data_, array('fields' => $fields))); ?>