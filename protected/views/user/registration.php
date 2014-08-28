<?php
/**
 * @var FForm $registrationForm
 * @var UserController $this
 */
?>
<div class="wrapper" style="background-color: #F4F4F4;">
  <?php $this->renderPartial('/_breadcrumbs');?>

  <h1><?php  echo Yii::app()->meta->setHeader('Регистрация')?></h1>

  <?php if( Yii::app()->user->isGuest ) {?>
    <?php if( !$registrationForm->getStatus() ) {?>
      <div class="text-container"><?php echo $this->textBlock('registration_text');?></div>
    <?php }?>
    <?php echo $registrationForm->render(); ?>
  <?php } else {?>
    <?php echo "Вы уже зарегистрированы."?>
 <?php }?>
</div>