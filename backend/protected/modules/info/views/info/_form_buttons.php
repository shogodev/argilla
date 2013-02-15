<div class="s-buttons s-buttons-additional">

  <?php
  $this->widget('bootstrap.widgets.TbButton', array('buttonType'  => 'link',
    'type'        => 'primary',
    'label'       => 'Очистить',
    'url'         => $this->createUrl('create'),
  ));
  ?>

  <?php
  if( $model->id )
  {
    $this->widget('bootstrap.widgets.TbButton', array('buttonType'  => 'link',
      'type'        => 'primary',
      'label'       => 'Добавить',
      'url'         => $this->createUrl('create', array('parent' => $model->id)),
    ));
  }
  ?>

  <?php $this->widget('bootstrap.widgets.TbButton', array('buttonType' => 'submit',
    'type'       => 'primary',
    'label'      => $model->isNewRecord ? 'Создать' : 'Применить',
  ));
  ?>

</div>
