<div class="s-buttons s-buttons-additional">

  <?php $this->widget('bootstrap.widgets.TbButton', array(
  'buttonType' => 'submit',
  'type'       => 'primary',
  'label'      => $model->isNewRecord ? 'Создать' : 'Применить',
)); ?>

  <?php $this->widget('bootstrap.widgets.TbButton', array(
  'buttonType'  => 'submit',
  'type'        => 'primary',
  'label'       => 'Сохранить',
  'htmlOptions' => array('name' => 'action', 'value' => 'index')
)); ?>

  <?php $this->widget('bootstrap.widgets.TbButton', array(
  'type'  => 'danger',
  'label' => 'Закрыть',
  'url'   => $this->getBackUrl()
)); ?>

</div>