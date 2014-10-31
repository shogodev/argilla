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
  ВАШ ЗАКАЗ ПОДТВЕРЖДЕН!
</div>

<div style="margin-bottom: 20px">
  <div style="font-size: 24px">Здравствуйте<?php echo !empty($model->name) ? ', '.$model->name : ''; ?>!</div>
  Благодарим вас за совершение покупок на <a href="<?php echo $host?>" target="_blank" style="color: #f88101"><?php echo $project?></a>! Ваш заказ был подтвержден и находится в обработке.
</div>
<div style="text-align: center; font-size: 30px; margin-bottom: 20px">
  Номер: <?php echo $model->id?><br />
  Дата: <?php echo $model->getDate()?>
</div>

<?php echo Yii::app()->controller->renderPartial('frontend.views.email._order_products', $_data_); ?>