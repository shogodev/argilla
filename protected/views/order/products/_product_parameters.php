<?php
/**
 * @var BasketController $this
 * @var Product $data
 * @var integer $index
 * @var ProductParameterName $item
 */
?>
<table>
  <?php if( $data->articul ) {?>
    <tr>
      <td>Артикул:</td>
      <td><?php echo $data->articul;?></td>
    </tr>
  <?php }?>
  <?php if( $items = $data->getCollectionItems() ) {?>
    <?php foreach($items as $item) {?>
      <tr>
        <td><?php echo $item->name?>:</td>
        <td><?php echo $item->value?></td>
      </tr>
    <?php }?>
  <?php }?>
</table>