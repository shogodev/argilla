<?php
/**
 * @var FForm $form
 * @var UserProfileController $this
 * @var array $_data_
 */
?>
<div class="wrapper">
  <?php $this->renderPartial('/_breadcrumbs');?>
</div>
<div class="white-body pre-footer">
  <div class="wrapper">
    <h1 class="uppercase s33 m20"><?php echo Yii::app()->meta->setHeader('Личный кабинет')?></h1>

    <div class="nofloat">
      <?php $this->renderPartial('_menu', $_data_)?>

      <section id="main" class="personal-page">
        <?php echo $form; ?>
      </section>
    </div>
  </div>
</div>
