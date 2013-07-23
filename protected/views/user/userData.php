<?php
/**
 * @var FForm $registrationForm
 * @var UserController $this
 */
?>
<div id="content" class="paddings">
  <?php $this->renderPartial('/breadcrumbs');?>

  <h1><?php echo $this->clip('h1', 'Профиль')?></h1>

  <?php echo $userForm; ?>
</div>
