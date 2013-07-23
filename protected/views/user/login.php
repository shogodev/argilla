<?php
/**
 * @var UserController $this
 */
?>
<div id="content" class="paddings">
  <?php $this->renderPartial('/breadcrumbs');?>
  <h1><?php echo $this->clip('h1', 'Вход')?></h1>
  <?php echo $this->loginForm->render()?>
</div>