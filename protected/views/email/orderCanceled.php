<?php
/**
 * @var CController $this
 *
 * @var Email $sender
 * @var string $subject
 * @var string $host
 * @var string $project
 * @var string $content
 * @var ContactField[] $emails
 * @var ContactField $email
 * @var ContactField[] $phones
 * @var ContactField $phone
 *
 * @var Order $model
 */
?>
<div style="font-size: 24px; font-weight: bold; text-align: center; margin-bottom: 30px">
  ВАШ ЗАКАЗ ОТМЕНЕН!
</div>

<div style="margin-bottom: 20px">
  <div style="font-size: 24px">Здравствуйте<?php echo !empty($model->name) ? ', '.$model->name : ''; ?>!</div>
  К сожалению, ваш заказ был отменен.</br>
  <?php echo !empty($orderComment) ? "<br />Причина: ".$orderComment : ''; ?>
</div>
<div style="text-align: center; font-size: 30px; margin-bottom: 20px">
  Номер: <?php echo $model->id?><br />
  Дата: <?php echo $model->getDate()?>
</div>

<?php echo Yii::app()->controller->renderPartial('frontend.views.email._order_products', $_data_); ?>