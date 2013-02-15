<?php
/**
 * User: tatarinov
 * Date: 21.12.12
 */
?>
<div class="wrap-info">
  <?php $this->renderPartial('/breadcrumbs');?>
</div>
<div class="wrap">
  <div class="container container_16 nofloat">
    <h1 class="h3"><?php echo $this->clip('h1', 'Вход')?></h1>
    <div class="text-container m20">
      <?php echo $this->textBlock('text_login_page')?>
    </div>
    <?php echo $this->loginForm->render()?>
  </div>
</div>