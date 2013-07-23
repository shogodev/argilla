<?php
/**
 * @var FForm $restoreForm
 * @var UserController $this
 */
?>
<div id="content" class="paddings">
  <?php $this->renderPartial('/breadcrumbs');?>

  <h1><?php echo $this->clip('h1', 'Восстановление пароля')?></h1>

  <?php echo $restoreForm; ?>
</div>

