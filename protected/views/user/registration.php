<?php
/**
 * @var FForm $registrationForm
 * @var UserController $this
 */
?>
<div class="wrapper">
  <?php $this->renderPartial('/_breadcrumbs');?>
</div>

<div class="white-body pre-footer">
  <div class="wrapper">
    <div class="nofloat m5">
      <h1 class="uppercase s33 fl"><?php  echo Yii::app()->meta->setHeader('Регистрация')?></h1>
    </div>

    <?php if( Yii::app()->user->isGuest ) {?>
      <?php if( !$registrationForm->getStatus() ) {?>
        <div class="text-container"><?php echo $this->textBlock('registration_text');?></div>
      <?php }?>
      <?php echo $registrationForm->render(); ?>
    <?php } else {?>
      <?php echo "Вы уже зарегистрированы."?>
    <?php }?>

  </div>
</div>