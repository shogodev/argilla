<?php
/**
 * @var FForm $registrationForm
 * @var UserController $this
 */
?>
<div id="content" class="paddings">
  <?php $this->renderPartial('/breadcrumbs');?>

  <h1><?php echo $this->clip('h1', 'Регистрация')?></h1>
  <?php if( Yii::app()->user->isGuest ) {?>

    <?php if( !$registrationForm->getStatus() ):?>
      <div class="text-container m30 registration-text">
        <?php echo $this->textBlock('registration_text');?>
      </div>
    <?php endif;?>

    <?php echo $registrationForm->render(); ?>

  <?php } else
    echo "Вы уже зарегистрированы." ?>
</div>