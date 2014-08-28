<?php
/**
 * @var FForm $form
 * @var UserController $this
 */
?>

<div class="wrapper" style="background-color: #F4F4F4;">

  <?php $this->renderPartial('/_breadcrumbs'); ?>
   <h1><?php echo $this->clip('h1', 'Личный кабинет') ?></h1>

   <?php $this->renderPartial('_menu', $_data_) ?>

   <h3 class="s18 m40">Смена пароля</h3>
   <div class="form">
      <?php echo $form; ?>
   </div>

</div>