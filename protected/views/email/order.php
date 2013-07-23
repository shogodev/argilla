<?php
/**
 * @var Order $model
 * @var Product[] $products
 */
?>
Здравствуйте<?php echo !empty($model->name) ? ', '.$model->name : ''; ?>!<br />
<br />Вы сделали заказ на сайте <?php echo Yii::app()->params->project; ?>
<br /><br />

<?php echo Yii::app()->controller->renderPartial('frontend.views.email.orderProducts', $_data_); ?>