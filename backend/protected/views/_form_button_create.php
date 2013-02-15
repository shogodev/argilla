<?php if( !Yii::app()->controller->popup ) { ?>
<div class="s-buttons s-buttons-top">
  <?php $this->widget('bootstrap.widgets.TbButton', array(
  'label' => 'Добавить',
  'url' => array('create'),
  'type' => 'info',
)); ?>
</div>
<?php } ?>