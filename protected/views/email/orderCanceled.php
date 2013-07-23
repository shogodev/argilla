<?php
/**
 * @var Order $model
 * @var string $orderComment
 * @var string $email
 */
?>

<div style="font-size: 14px">
  Здравствуйте<?php echo !empty($model->name) ? ', '.$model->name : ''?>!<br />
  <br /><br />
  <?php echo Yii::app()->controller->renderPartial('frontend.views.email.orderProducts', $_data_); ?>
  К сожалению, ваш заказ с номером #<?php echo $model->id; ?> не принят.
  <?php echo !empty($orderComment) ? "<br />Причина: ".$orderComment : ''; ?>
</div>