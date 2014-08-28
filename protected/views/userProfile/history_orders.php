<?php
/**
 * @var FForm $form
 * @var UserController $this
 * @var array $_data_
 * @var Order[] $orders
 */
?>
<div id="content" class="wrapper">
  <?php $this->renderPartial('/_breadcrumbs');?>

  <div class="nofloat profile">
    <h1><?php echo $this->clip('h1', 'Личный кабинет')?></h1>
    <?php $this->renderPartial('_menu', $_data_) ?>
    <div class="profile-content">
      <h2>История заказов</h2>
      <?php if( $orders ) {?>
        <?php $this->widget('FListView', array(
          'dataProvider' => new FArrayDataProvider($orders, array('pagination' => false)),
          'itemView' => '_orders_block',
        ));?>
      <?php } else {?>
        Нет заказов.
      <?php }?>
    </div>
  </div>
</div>
