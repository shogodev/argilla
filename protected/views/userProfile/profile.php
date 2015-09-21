<?php
/**
 * @var UserProfileController $this
 * @var User $model
 * @var array $_data_
 */
?>
<div class="wrapper">
  <div class="breadcrumbs-offset m25">
    <?php $this->renderPartial('/_breadcrumbs');?>
  </div>

  <h1 class="uppercase s33 m20"><?php echo Yii::app()->meta->setHeader('Профиль')?></h1>

  <div class="nofloat m50">
    <?php $this->renderPartial('_menu', $_data_) ?>

    <section id="main" class="personal-page">
      <div><?php echo $model->profile->name?></div>
      <div><?php echo $model->login?></div>
    </section>
  </div>
</div>
