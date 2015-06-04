<?php
/**
 * @var BController $this
 * @var BProductOption $model
 * @var BGridView $grid
 */
?>
<div class="s-buttons s-buttons-top">
  <?php
  $this->widget('BAssignerButton', array(
    'label' => 'Добавить',
    'assignerOptions' => array(
      'iframeUrl' => $this->createUrl("/product/option/option/create", array('product_id' => $model->id, 'popup' => true)),
      'updateGridId' => $grid->id
    ),
    'type' => 'info',
    'popupDepended' => true,
  ));
  ?>
</div>