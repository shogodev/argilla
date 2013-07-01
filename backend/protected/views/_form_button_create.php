<?php
/**
 * @var BController $this
 * @var BActiveRecord $model
 */
?>

<div class="s-buttons s-buttons-top">
  <?php $this->widget('BButton', array(
    'label' => 'Добавить',
    'url' => $this->createUrl('create', [get_class($model) => Yii::app()->request->getQuery(get_class($model), array())]),
    'type' => 'info',
    'popupDepended' => true,
  )); ?>
</div>