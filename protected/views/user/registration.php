<?php
/**
 * @var FForm $registrationForm
 * @var FController $this
 */
?>
<div class="wrap-info">
  <?php $this->renderPartial('/breadcrumbs');?>
</div>
<div class="wrap">
  <div class="container container_16 nofloat">
    <h1 class="h3"><?php echo $this->clip('h1', 'Регистрация нового пользователя')?></h1>
    <?php if( Yii::app()->user->isGuest ) {?>

      <?php if( !$registrationForm->getStatus() ):?>
      <div class="text-container m30">
        <?php echo $this->textBlock('registration_text');?>
      </div>
      <?php endif;?>

      <?php echo $registrationForm->render(); ?>

    <?php } else
      echo "Вы уже зарегистрированы." ?>
  </div>
</div>