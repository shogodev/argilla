<?php
/**
 * @var BController $this
 */
?>
<div class="s-buttons s-buttons-top">
  <?php $this->widget('BButton', array(
    'label' => 'Добавить',
    'url' => $this->createUrl('create', array('modificationParent' => Yii::app()->request->getParam('id'))),
    'type' => 'info',
    'popupDepended' => true,
  )); ?>
</div>