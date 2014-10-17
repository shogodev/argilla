<?php
/**
 * @var BasketController $this
 * @var integer $orderId
 */
?>
<div class="wrapper">
  <?php $this->renderPartial('/_breadcrumbs');?>

  <div class="center">
    <?php echo $this->textBlockRegister('Заказ принят', null, null, array('{orderId}' => $orderId))?>
  </div>
</div>