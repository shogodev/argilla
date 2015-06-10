<?php
/**
 * @var Product $model
 */
 ?>

<?php if( $parameters ) { ?>
  <div id="params">
    <div class="h1 bb uppercase caption center m20">Характеристики</div>
    <div class="nofloat m30">
      <?php foreach(Arr::divide($parameters, 2) as $i => $part) { ?>
      <div class="<?php echo $i == 0 ? 'l' : 'r'?>-main">
        <table class="zero params-table">
          <?php foreach($part as $parameter) { ?>
          <tr>
            <th><?php echo $parameter->name?>:</th>
            <td><?php echo $parameter->value?></td>
          </tr>
          <?php } ?>
        </table>
      </div>
      <?php } ?>
    </div>
  </div>
<?php } ?>