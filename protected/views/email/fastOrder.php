<?php
/**
 * @var Order $model
 */
?>
  <div style="padding-bottom: 20px; font-size: 30px; font-weight: bold; color: #fba91a">
    ВАШ ЗАКАЗ ПРИНЯТ
  </div>
  <div style="padding-bottom: 20px">
    <span style="font-weight: bold; font-size: 24px">Здравствуйте<?php echo !empty($model->name) ? ', '.$model->name : ''; ?>!</span><br />
    Благодарим Вас за совершение покупок на Dverivtapkah! Ваш заказ был успешно принят и находится в обработке.
  </div>
  <div style="padding-bottom: 25px">
    В ближайшее время наш менеджер свяжется с Вами для уточнения необходимых нюансов,<br />
    после чего Вы сможете оплатить заказ. Мы отправим Ваш заказ в течение суток с момента поступления денег.
  </div>
<?php if( isset($model->products[0]) ) {?>
  <?php $product = $model->products[0]?>
  <div style="text-align: center">
    <?php if( !empty($product->history->url) ) {?>
      <a href="<?php echo Yii::app()->request->hostInfo.$product->history->url?>" style="color: #373731; text-decoration: none; font-weight: bold" target="_blank"><?php echo $product->name?></a><br />
      <a href="<?php echo Yii::app()->request->hostInfo.$product->history->url?>">
        <img src="<?php echo Yii::app()->request->hostInfo.'/'.$product->history->img?>" alt="" width="105" style="margin-top: 10px" />
      </a>
    <?php } else {?>
      <a style="color: #373731; text-decoration: none; font-weight: bold" target="_blank"><?php echo $product->name?></a><br />
      <a><img src="<?php echo Yii::app()->request->hostInfo.'/'.$product->history->img?>" alt="" width="105" style="margin-top: 10px" /></a>
    <?php }?>
  </div>
<?php }?>