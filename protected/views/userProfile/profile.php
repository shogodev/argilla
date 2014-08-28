<?php
/**
 * @var UserProfileController $this
 * @var User $model
 * @var array $_data_
 */
?>
<div class="wrapper" style="background-color: #F4F4F4;">
  <?php $this->renderPartial('/_breadcrumbs');?>

  <div class="nofloat profile">
    <h1><?php echo Yii::app()->meta->setHeader('Личный кабинет')?></h1>

    <?php $this->renderPartial('_menu', $_data_)?>

    <?php echo $model->profile->name?>
  </div>
</div>
