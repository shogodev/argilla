<div class="s-buttons s-buttons-top">
<?php $this->widget('bootstrap.widgets.TbButton', array(
  'label' => 'Добавить группу',
  'url' => array('create', 'parent' => BProductParamName::ROOT_ID, 'section_id' => $model->section_id),
  'type' => 'info',
)); ?>
</div>