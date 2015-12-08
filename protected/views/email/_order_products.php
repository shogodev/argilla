<?php
/**
 * @var CController $this
 *
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
Yii::import('frontend.models.order.delivery.OrderDeliveryType');
?>
<?php if( !empty($model->products) ) { ?>
  <table style="border-collapse: collapse; width: 100%; margin-bottom: 20px">
    <tr>
      <td style="text-align: center; border: 1px solid #333; padding: 10px 30px">Наименование</td>
      <td style="text-align: center; border: 1px solid #333; padding: 10px 30px">Артикул</td>
      <td style="text-align: center; border: 1px solid #333; padding: 10px 30px">Количество</td>
      <td style="text-align: center; border: 1px solid #333; padding: 10px 30px">Цена, руб.</td>
    </tr>
    <?php foreach( $model->products as $key => $product ) { ?>
      <tr>
        <td style="border: 1px solid #333; padding: 10px 30px">
          <table>
            <tr>
              <td>
                <?php if( $product->history->img ) {?>
                <a href="<?php echo $host.$product->history->url?>">
                  <img src="<?php echo $host.$product->history->img?>" alt="" width="150" />
                </a>
                <?php }?>
              </td>
              <td style="padding-left: 15px">
                <div style="font-size: 16px"><?php echo $product->name?></div>
                <?php if( $parameters = $product->getItems('ProductParameter', array('ProductOption')) ) {?>
                  <?php foreach($parameters as $parameter) {?>
                    <div style="font-size: 16px"><?php echo $parameter->name?>: <?php echo $parameter->value?></div>
                  <?php }?>
                <?php }?>
              </td>
            </tr>
          </table>
        </td>
        <td style="border: 1px solid #333; padding: 10px 30px; text-align: center">
          <?php echo $product->history->articul?>
        </td>
        <td style="border: 1px solid #333; padding: 10px 30px; text-align: center">
          <?php echo $product->count?>
        </td>
        <td style="border: 1px solid #333; padding: 10px 30px; text-align: center">
          <span style="font-size: 22px; font-weight: bold"><?php echo PriceHelper::price($product->sum)?></span>
        </td>
      </tr>
    <?php }?>
    <?php if( $options = $product->getItems('ProductOption') ) {?>
      <?php foreach($options as $option) {?>
        <tr>
          <td style="background: #f2f2f2; border: 1px solid #e5e5e5">
            <?php echo $option->value?>
          </td>
          <td style="background: #f2f2f2; border: 1px solid #e5e5e5; text-align: center">
          </td>
          <td style="background: #f2f2f2; border: 1px solid #e5e5e5; text-align: center">
            <?php echo $option->amount?>
          </td>
          <td style="background: #f2f2f2; border: 1px solid #e5e5e5; text-align: center">
            <span style="font-size: 22px; font-weight: bold"><?php echo PriceHelper::price($option->price * $option->amount)?></span>
          </td>
        </tr>
      <?php }?>
    <?php }?>
    <tr><td style="height: 10px" colspan="4">&nbsp;</td></tr>
    <?php if( isset($model->discount) && PriceHelper::isNotEmpty($model->discount) ) {?>
      <tr>
        <td colspan="3" style="text-align: right; padding-right: 20px; font-size: 16px">
          Скидка:
        </td>
        <td style="text-align: center; font-size: 22px; font-weight: bold">
          <?php echo PriceHelper::price($model->discount)?>%
        </td>
      </tr>
    <?php }?>
    <?php if( PriceHelper::isNotEmpty($model->deliveryPrice) || $model->delivery->deliveryType->isFreeDelivery($model->sum)) {?>
      <tr>
        <td colspan="3" style="text-align: right; padding-right: 20px; font-size: 16px">
          Доставка:
        </td>
        <td style="text-align: center; font-size: 22px; font-weight: bold">
          <?php if( $model->delivery->deliveryType->isFreeDelivery($model->sum) ) {?>
            Бесплатно
          <?php } else {?>
            <?php echo PriceHelper::price($model->deliveryPrice, ' руб.')?>
          <?php }?>
        </td>
      </tr>
    <?php }?>
    <tr>
      <td colspan="3" style="text-align: right; padding-right: 20px; font-size: 16px">
        Стоимость выбранных товаров и услуг:
      </td>
      <td style="text-align: center; font-size: 22px; font-weight: bold">
        <?php echo PriceHelper::price($model->totalSum, ' руб.')?>
      </td>
    </tr>
  </table>  
<?php }?>