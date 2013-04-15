<?php
/* @var BProductController $this */
/* @var BActiveDataProvider $dataProvider */
/* @var BProduct $model */

Yii::app()->breadcrumbs->show();

$this->widget('BCustomColumnsGridView', array(
  'dataProvider' => $model->search(),
  'filter' => $model,
  'columns' => array(
    array(
      'name' => 'BProduct',
      'header' => 'Продукты',
      'class' => 'BAssociationColumn',
      'iframeAction' => 'index',
    ),
  ),
));