<?php
/**
 * @var PaymentController $this
 * @var Order $order
 */
?>

<div class="wrapper">

  <?php $this->renderOverride('_breadcrumbs');?>

  <div class="nofloat m20">

    <h1><?php echo Yii::app()->meta->setHeader('Оплата успешно произведена')?></h1>
    <?php echo $this->textBlockRegister('Оплата успешно произведена', 'Оплата успешно произведена.', array('class' => 'success-message bb center'))?>

  </div>
</div>