<?php
/**
 * User: Nikita Melnikov <melnikov@shogo.ru>
 * Date: 1/6/13
 *
 * @var BCommentController $this
 * @var Comment $model
 * @var SActiveDataProvider $dataProvider
 */
?>
<?php Yii::app()->breadcrumbs->show();?>
<?php $this->widget('BGridView', array(
  'filter' => $model,
  'dataProvider' => $model->search(),
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

    array('class' => 'JToggleColumn', 'name' => 'visible', 'filter' => CHtml::listData($model->yesNoList(), 'id', 'name')),
    array('class' => 'BButtonColumn'),
  ),
)); ?>