<?php
/**
 * @var FForm $restoreForm
 * @var UserController $this
 */
?>
<div class="wrapper" style="background-color: #F4F4F4;">
  <?php $this->renderPartial('/_breadcrumbs');?>

  <h1><?php echo Yii::app()->meta->setHeader('Восстановление пароля') ?></h1>

  <?php echo $restoreForm; ?>
</div>