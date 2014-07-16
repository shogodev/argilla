<?php
/**
 * @var FForm $form
 * @var UserProfileController $this
 * @var array $_data_
 */
?>
<div class="wrapper" style="background-color: #F4F4F4;">
  <?php $this->renderPartial('/_breadcrumbs');?>

  <div class="nofloat profile">
    <h1><?php echo Yii::app()->meta->setHeader('Личный кабинет')?></h1>

    <?php $this->renderPartial('_menu', $_data_)?>

    <h3 class="s18 m40">Личные данные</h3>

    <div class="form">
      <?php echo $form; ?>
    </div>
  </div>
</div>
