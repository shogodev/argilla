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
<div style="font-size:36px;text-align:center;margin-bottom: 40px;">
  Ваш заказ принят.
</div>
<p style="font-size:24px;margin-bottom:7px;">Здравствуйте<?php echo !empty($model->name) ? ', '.$model->name : ''; ?>!</p>
<p style="margin-bottom:15px;">Благодарим Вас за совершение покупок на <a target="_blank" style="color:#c92128;" href="<?php echo $host?>"><?php echo $project?></a>! Ваш заказ был успешно принят и находится в обработке.</p>
<div style="text-align:center;font-size:30px;margin:40px 0 40px;line-height:40px;">
  Номер:  <?php echo $model->id?><br>
  Дата:   <?php echo $model->getDate()?>
</div>
<p style="margin-bottom: 25px;">В ближайшее время наш менеджер свяжется с Вами для уточнения необходимых нюансов, после чего Вы сможете оплатить заказ. Мы отправим Ваш заказ в течение суток с момента поступления денег.</p>

<?php echo Yii::app()->controller->renderPartial('frontend.views.email._order_products', $_data_); ?>

<div style="font-size:14px;">
  Если Вам необходима помощь или у Вас возникли вопросы по Вашему заказу, напишите нам на <?php if($email) echo '<a href="mailto:'.$email.'" style="color: #949494">'.$email.'</a>'?> <br>
  или позвоните по телефону
  <?php foreach($phones as $phone) {?>
    <span style="font-weight: bold"><span style="color: #c92128"><?php echo $phone->value?></span> <?php echo $phone->description?></span><br />
  <?php }?>
  Пожалуйста, не забудьте указать в письме номер Вашего заказа.
</div>