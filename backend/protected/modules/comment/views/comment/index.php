<?php
/**
 * @var BCommentController $this
 * @var BComment $model
 * @var BActiveDataProvider $dataProvider
 */
?>

<?php Yii::app()->breadcrumbs->show();?>

<?php $this->widget('BGridView', array(
  'filter' => $model,
  'dataProvider' => $dataProvider,
  'columns' => array(
    array('name' => 'id', 'htmlOptions' => array('class' => 'center span1'), 'filter' => false),
    array(
      'name' => 'model',
      'filter' => CHtml::listData(BComment::model()->findAll(), 'model', 'model')
    ),
    array(
      'value' => 'BFrontendUser::model()->findByPk($data->user_id)->login',
      'name' => 'user_id',
      'filter' => CHtml::listData(BFrontendUser::model()->findAll(),'id','login'),
    ),
    array('name' => 'message', 'value' => 'CHtml::encode($data->message);'),

    array('class' => 'JToggleColumn', 'name' => 'visible'),
    array('class' => 'BButtonColumn'),
  ),
)); ?>