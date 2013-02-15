<?php
/**
 * @var Product $model
 */
 ?>

<div class="grid_9 first m30" id="specification-anchor">
  <h3 class="left">Параметры <?php echo $model->name?></h3>
  <div class="equip-tabs">

    <div style="clear: both">
      <table class="zero eqSpecs">

        <?php foreach($model->parameters as $parameter) { ?>
        <?php if( empty($parameter->value) ) continue;?>
        <tr>
          <th>
            <?php if( $parameter->img ) { ?>
            <div class="specs-icon"><img src="<?php echo $parameter->img?>" alt="" /></div>
            <?php } ?>
            <?php echo $parameter->name?></th>
          <td><?php echo $parameter->value?></td>
        </tr>
        <?php } ?>

        <tr>
          <th>Артикул</th>
          <td><?php echo $model->articul?></td>
        </tr>

      </table>
    </div>
  </div>
</div>