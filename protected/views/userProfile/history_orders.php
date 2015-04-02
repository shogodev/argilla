<?php
/**
 * @var FForm $form
 * @var UserController $this
 * @var array $_data_
 * @var FActiveDataProvider $orderDataProvider
 */
?>
<div class="wrapper">
  <?php $this->renderPartial('/_breadcrumbs');?>
</div>
<div class="white-body pre-footer">
  <div class="wrapper">
    <h1 class="uppercase s33 m20"><?php echo Yii::app()->meta->setHeader('Личный кабинет')?></h1>

    <div class="nofloat">
      <?php $this->renderPartial('_menu', $_data_) ?>

      <section id="main" class="personal-page">

        <?php $this->widget('FListView', array(
          'template' => '{items}<div class="nofloat"><div class="pager fr">{pager}</div></div>',
          'dataProvider' => $orderDataProvider,
          'itemView' => '_orders_block',
          'emptyText' => 'Нет заказов.',
          'pagerCssClass' => 'pager fr',
          'pager' => array(
            'class' => 'FLinkPager',
            'maxButtonCount' => 6,
          )
        ));?>

      </section>
    </div>
  </div>
</div>