<?php
/**
 * @var BasketController $this
 * @var Product $data
 * @var integer $index
 */
?>
<tr>
  <td>
    <?php if( $data->image ) {?>
      <a href="<?php echo $data->getUrl()?>" class="product-pic">
        <img src="<?php echo $data->image->pre;?>" alt="" />
      </a>
    <?php }?>
    <div>
      <div class="m10">
        <a class="h1 uppercase s14" href="<?php echo $data->getUrl()?>"><?php echo $data->name?></a>
      </div>
      <?php $this->renderPartial('products/_product_parameters', $_data_);?>
    </div>
  </td>
  <td class="opensans s24 bb"><?php echo PriceHelper::price($data->getPrice())?></td>
  <td>
      <span class="spinner">
        <span class="spinner-down"></span>
        <?php echo $this->basket->inputAmountCollection($data, array('class' => 'inp')) ?>
        <span class="spinner-up"></span>
      </span>
  </td>
  <td class="opensans s24 bb"><?php echo PriceHelper::price($data->getSum())?></td>
  <td>
    <?php echo $this->basket->buttonRemove($data, '', array('class' => 'remove-btn'))?>
  </td>
</tr>