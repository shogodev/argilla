<?php
/**
 * @var User $model
 * @var UserProfile $profile
 * @var string $adminUrl
 */
?>
<?php
$fields = array(
  'ID' => $model->id,
  'Логин' => $model->login,
  'E-mail' => $model->email
);

foreach($profile->attributeLabels() as $attribute => $label)
  if( !empty($profile->$attribute) )
    $fields[$label] = $profile->$attribute;

$data = array(
 'header' => "Регистрация пользователя",
 'top' => '',
 'fields' => $fields,
 'bottom' => '',
);
?>

<?php echo Yii::app()->controller->renderPartial('frontend.views.email._backendTemplate', CMap::mergeArray($_data_, $data)); ?>