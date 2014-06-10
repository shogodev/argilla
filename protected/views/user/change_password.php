<?php
/**
 * @var FForm $form
 * @var UserController $this
 */
?>

<div id="content">
  <div class="wrapper">
    <?php $this->renderPartial('/_breadcrumbs'); ?>

    <div class="nofloat profile">
      <h1><?php echo $this->clip('h1', 'Личный кабинет') ?></h1>
      <?php $this->renderPartial('_left_menu', $_data_) ?>
      <div class="profile-content">
        <h2>Смена пароля</h2>

        <div class="form">
          <?php echo $form; ?>
        </div>
      </div>
    </div>
  </div>
</div>
