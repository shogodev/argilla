<?php
/**
 * @var PaymentController $this
 * @var Order $order
 * @var string $error
 * @var string $textBlock
 */
?>

<div class="wrapper">

  <?php $this->renderOverride('_breadcrumbs');?>

  <div class="nofloat m20">

    <h1><?php echo $this->clip('h1', 'Оплата не произведена')?></h1>
    <?php echo $textBlock?>

  </div>
</div>