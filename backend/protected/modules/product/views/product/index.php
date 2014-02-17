<?php
/**
 * @var BProductController $this
 * @var BProduct $model
 * @var BActiveDataProvider $dataProvider
 */
?>

<?php Yii::app()->breadcrumbs->show();

$this->widget('BCustomColumnsGridView', array(
  'dataProvider' => $dataProvider,
  'filter' => $model,
  'columns' => array(
    array(
      'name' => 'BProduct',
      'header' => 'Продукты',
      'class' => 'BPopupColumn',
      'iframeAction' => 'index',
    ),
  ),
));