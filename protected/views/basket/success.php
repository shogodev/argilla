<?php
/**
 * @var BasketController $this
 */
?>
<div id="content" class="paddings">
  <?php $this->renderPartial('/breadcrumbs');?>

  <div class="nofloat m10">
    <h1 class="left"><?php echo $this->clip('h1', 'Корзина')?></h1>
  </div>
  <?php echo $this->textBlockRegister('Заказ принят', 'Заказ принят')?>
</div>