<?php
/**
 * @var UserController $this
 * @var FForm $loginForm
 */
?>
<div class="red-skew-block page-caption">
  <div class="wrapper">
    <?php $this->renderPartial('/breadcrumbs');?>
    <h1 class="h2 white s40 m30"><?php echo $this->clip('h1', 'Вход')?></h1>
  </div>
</div>

<div class="red-backskew-end">
  <div class="wrapper nofloat m50">
    <div class="m45"></div>
    <?php echo $loginForm->render()?>
  </div>
</div>
