<?php
/**
 * @var BasketController $this
 * @var integer $orderId
 */
?>
<?php $this->renderPartial('/_breadcrumbs');?>

<?php $phonesText = array();?>
<?php foreach(ViewHelper::phones() as $phone) {?>
  <?php $phonesText[] = '<p class="m45" style="font-size:44px;margin-top:15px"><span class="blue">'.$phone->value.'</span> '.$phone->description.'</p>';?>
<?php }?>

<?php $defaultText = '<div class="container text-container m35 center">
  <p class="s23 upcase m15">Ваш заказ принят</p>
  <p class="m20 s17">
    Ваш заказ был успешно принят и находится в обработке. <br>
    Номер Вашего заказа: {orderId}<br>
    В ближайшее время менеджер свяжется с Вами для уточнения <br>
    необходимых деталей. <br>
    После этого вы сможете оплатить заказ. <br>
    Благодарим Вас за совершение покупок на {projectName}<br>
  </p>
  {phones}
  <p><a href="{mainUrl}" class="medium-btn upcase s20 green-btn">на главную</a></p>
</div>';?>

<?php echo $this->textBlockRegister('Заказ принят', $defaultText, null, array(
  '{projectName}' => Yii::app()->params->project,
  '{orderId}' => $orderId,
  '{mainUrl}' => $this->createUrl('index/index'),
  '{phones}' => count($phonesText) > 0 ? '<p class="s17">Вы можете задать вопрос по '.Utils::plural(count($phonesText), 'телефону|телефонам').':</p>'.implode('', $phonesText) : '',
))?>
