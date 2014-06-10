<?php
/**
 * @var UserController $this
 * @var FForm $loginForm
 */
?>
<div class="red-skew-block page-caption">
  <div class="wrapper">
    <?php $this->renderPartial('/_breadcrumbs');?>
    <h1><?php echo $this->clip('h1', 'Вход')?></h1>
    <?php echo $loginForm->render()?>
  </div>
</div>