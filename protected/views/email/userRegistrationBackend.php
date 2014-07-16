<?php
/**
 * @var User $model
 * @var UserProfile $userData
 * @var string $adminUrl
 */
?>
<?php
$fields = array(
  'ID' => $model->id,
  'Логин' => $model->login,
  'E-mail' => $model->email
);

foreach($userData->attributeLabels() as $attribute => $label)
  if( !empty($userData->$attribute) )
    $fields[$label] = $userData->$attribute;

$data = array(
 'header' => "Регистрация ползвателя",
 'top' => '',
 'fields' => $fields,
 'bottom' => '',
);
?>

<?php echo Yii::app()->controller->renderPartial('frontend.views.email._backendTemplate', CMap::mergeArray($_data_, $data)); ?>