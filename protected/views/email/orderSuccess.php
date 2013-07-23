<?php
/**
 * @var Order $model
 */
?>

<div style="font-size: 14px">
  Здравствуйте<?php echo !empty($model->name) ? ', '.$model->name : ''?>!<br />
  <br />Вы сделали заказ на нашем сайте.
  <br /><br />
  <?php echo Yii::app()->controller->renderPartial('frontend.views.email.orderProducts', $_data_); ?>
  <br /><br /><br /><br />
  Ваш заказ принят и обрабатывается нашими менеджерами.
  <br /><br /><br /><br />
</div>