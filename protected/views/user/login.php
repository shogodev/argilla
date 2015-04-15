<?php
/**
 * @var UserController $this
 * @var FForm $loginForm
 */
?>
<div class="wrapper">
  <?php $this->renderPartial('/_breadcrumbs');?>
</div>

<div class="white-body pre-footer">
  <div class="wrapper">
    <div class="nofloat m5">
      <h1 class="uppercase s33 fl"><?php echo Yii::app()->meta->setHeader('Вход')?></h1>
    </div>

    <?php echo $loginForm->render()?>

  </div>
</div>