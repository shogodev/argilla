<?php
  /**
  * @var BTagController $this
  * @var BTag $model
  */
  Yii::app()->breadcrumbs->show();

  $this->widget('BGridView', array(
  'filter' => $model,
  'dataProvider' => $model->search(),
  'columns' => array(
      array('name' => 'id', 'class' => 'BPkColumn', 'htmlOptions' => array('class' => 'center span1'), 'filter' => false),
      array('name' => 'name', 'htmlOptions' => array('class' => 'span6'), 'class' => 'OnFlyEditField'),
      array('name' => 'group', 'filter' => TagModule::$tagGroupList),
      array('class' => 'BButtonColumn'),
    ),
  ));