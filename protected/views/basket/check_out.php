<?php
/**
 * @var BasketController $this
 * @var FForm $form
 */
?>
<div class="wrapper">
  <?php $this->renderPartial('/_breadcrumbs');?>

  <div class="basket-steps">
    <a href="<?php echo $this->createUrl('basket/index')?>" class="step">Шаг 1: Выбор товаров</a>
    <span class="step active">Шаг 2: Выбор метода доставки и оплаты</span>
  </div>

  <div class="caption m30">
    <h1>Корзина</h1>
  </div>

  <?php echo $form?>

  <div class="basket-steps m50">
    <a href="<?php echo $this->createUrl('basket/index')?>" class="step">Шаг 1: Выбор товаров</a>
    <span class="step active">Шаг 2: Выбор метода доставки и оплаты</span>
  </div>
</div>