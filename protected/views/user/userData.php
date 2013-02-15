<?php
/**
 * @var FForm $registration_form
 * @var FController $this
 */
?>
<div class="wrap-info">
  <?php $this->renderPartial('/breadcrumbs');?>
</div>

<div class="wrap">
  <div class="container container_16 nofloat">
    <h1 class="h3"><?php echo $this->clip('h1', 'Профиль')?></h1>
    <?php echo $userForm; ?>
  </div>
</div>
