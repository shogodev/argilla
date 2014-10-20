<?php
/**
 * @var BasketController $this
 * @var FForm $form
 */
?>
<div id="content" class="paddings">
  <?php $this->renderPartial('/breadcrumbs');?>

  <div class="nofloat m10">
    <h1 class="left"><?php echo Yii::app()->meta->setHeader('Корзина')?></h1>
    <div class="right">
      <a href="<?php echo $this->basket->url?>" class="btn grey-btn basket-step">Шаг 1: Выбор товаров</a>
      <span class="btn black-btn basket-step">Шаг 2: Выбор метода доставки и оплаты</span>
    </div>
  </div>

  <?php echo $form?>

  <div class="nofloat m10">
    <div class="right">
      <a href="<?php echo $this->basket->url?>" class="btn grey-btn basket-step">Шаг 1: Выбор товаров</a>
      <span class="btn black-btn basket-step">Шаг 2: Выбор метода доставки и оплаты</span>
    </div>
  </div>
</div>