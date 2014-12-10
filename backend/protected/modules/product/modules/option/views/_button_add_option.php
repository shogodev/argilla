<?php
/**
 * @var BController $this
 * @var BProductOption $model
 * @var BGridView $grid
 */
?>
<div class="s-buttons s-buttons-top">
  <?php
  $buttonId = 'add_option_button_'.$model->id;
  $closeOperation = "function(){ $.fn.yiiGridView.update('{$grid->id}')}";

  $this->widget('BButton', array(
    'htmlOptions' => array('id' => $buttonId),
    'label' => 'Добавить',
    'url' => $this->createUrl("option/option/create", array('product_id' => $model->id, 'popup' => true)),
    'type' => 'info',
    'popupDepended' => true,
  ));

  $cs = Yii::app()->getClientScript();
  $cs->registerScript($buttonId.'_script', "
    jQuery(document).on('click', '#{$buttonId}', function(e){
      e.preventDefault();
      assigner.open(this.href, {'closeOperation' : {$closeOperation}});
    });
  ");
  ?>
</div>