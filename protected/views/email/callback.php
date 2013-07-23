<?php
/**
 * @var $this
 * @var Callback $model
 * @var string $adminUrl
 */
?>
<?php
$fields = array();

foreach($model->attributeLabels() as $attribute => $label)
  $fields[$label] = $model->$attribute;

$data = array(
  'header' => "Заказ обратного звонка",
  'top' => '',
  'fields' => $fields,
  'bottom' => '',
);
?>

<?php echo Yii::app()->controller->renderPartial('frontend.views.email._backendTemplate', CMap::mergeArray($_data_, $data)); ?>