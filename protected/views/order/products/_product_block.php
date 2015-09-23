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
      <a href="<?php echo $data->getUrl()?>">
        <img src="<?php echo $data->image->pre;?>" alt="<?php echo $data->name ?>">
      </a>
    <?php }?>
    <a href="<?php echo $data->getUrl()?>">
      <?php echo $data->name?>
    </a>
    <?php $this->renderPartial('products/_product_parameters', $_data_);?>
  </td>
  <td>
    <?php echo PriceHelper::price($data->getPrice())?>
  </td>
  <td>
    <?php echo $this->basket->inputAmountCollection($data, array('class' => '')) ?>
  </td>
  <td>
    <?php echo PriceHelper::price($data->getSum())?>
  </td>
  <td>
    <?php echo $this->basket->buttonRemove($data, 'Удалить', array('class' => ''))?>
  </td>
</tr>
