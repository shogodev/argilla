<?php
 /**
 *  @var Order $model
 */
?>
<?php if( !empty($model->products) ) { ?>
<table width="100%" align="left" cellspacing="0" style="margin-bottom:20px">
  <tr>
    <th style="padding:2px 5px; border-bottom: 5px solid #D8D8D8" align="center">Наименование</th>
    <th style="padding:2px 10px; border-bottom: 5px solid #D8D8D8" align="center">Параметры</th>
    <th style="padding:2px 10px; border-bottom: 5px solid #D8D8D8" align="center">Стоимость, руб.</th>
  </tr>
  <?php foreach( $model->products as $key => $product ) { ?>
  <tr>
    <td style="padding:2px 5px; border-bottom: 5px solid #EFEFEF">
      <a target="_blank" href="<?php echo Yii::app()->request->hostInfo.$product->history->url?>">
        <?php echo $product->name?>
      </a>
    </td>
    <td align="center" style="padding:2px 10px; border-bottom: 5px solid #EFEFEF">
      <?php if( $product->items ) {?>
        <table cellspacing="0" style="border-collapse: collapse; font-size: 12px">
          <tr>
            <th colspan="2" style="border: 1px solid #ccc; padding: 0 5px"></th>
            <th style="border: 1px solid #ccc; padding: 0 5px"><b>Количество</b></th>
          </tr>
          <?php foreach($product->items  as $item) {?>
            <tr>
              <th valign="top" align="left" style="border: 1px solid #ccc; padding: 0 5px"><?php echo $item['name']?></th>
              <td style="border: 1px solid #ccc; padding: 0 5px" align="center"><?php echo $item['value']?></td>
              <td style="border: 1px solid #ccc; padding: 0 5px" align="center"><?php echo $item['amount']?></td>
            </tr>
          <?php }?>
        </table>
      <?php }?>
    </td>
    <td align="center" style="padding:2px 10px; border-bottom: 5px solid #EFEFEF"><b><?php echo Yii::app()->format->formatNumber($product->sum)?></b></td>
  </tr>
  <?php } ?>
  <tr>
    <th colspan="3" style="padding:2px 5px"></th>
  </tr>
  <?php if( isset($model->discount) && !Utils::decimalEmpty($model->discount) ) { ?>
  <tr>
    <td align="right" colspan="2" style="padding:2px 5px">Скидка:</td>
    <td align="center" style="padding:2px 5px">
      <b><?php echo Yii::app()->format->formatNumber($model->discount); ?> руб.</b>
    </td>
  </tr>
  <?php } ?>
  <tr>
    <td align="right" colspan="2" style="padding:2px 5px">Итоговая стоимость:</td>
    <td align="center" style="padding:2px 5px">
      <b><?php echo Yii::app()->format->formatNumber($model->sum); ?> руб.</b>
    </td>
  </tr>
</table>
<?php }?>