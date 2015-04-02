<?php
/**
 * @var FForm $restoreForm
 * @var UserController $this
 */
?>
<div class="wrapper">
  <?php $this->renderPartial('/_breadcrumbs');?>
</div>

<div class="white-body pre-footer">
  <div class="wrapper">
    <div class="nofloat m5">
      <h1 class="uppercase s33 fl"><?php echo Yii::app()->meta->setHeader('Восстановление пароля') ?></h1>
    </div>

    <?php echo $restoreForm; ?>

  </div>
</div>
