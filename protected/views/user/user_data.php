<?php
/**
 * @var FForm $form
 * @var UserController $this
 * @var array $_data_
 */
?>
<div id="content" class="wrapper">
  <?php $this->renderPartial('/_breadcrumbs');?>

  <div class="nofloat profile">
    <h1><?php echo $this->clip('h1', 'Личный кабинет')?></h1>
    <?php $this->renderPartial('_left_menu', $_data_)?>
    <div class="profile-content">
      <h2>Личные данные</h2>
      <div class="form">
        <?php echo $form; ?>
      </div>
    </div>
  </div>
</div>
