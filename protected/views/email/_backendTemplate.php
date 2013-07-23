<?php
/**
 * @var string $header
 * @var string $top
 * @var array $fields
 * @var string $bottom
 * @var string $adminUrl
 */
?>
<h1><?php echo $header?> на сайте <?php echo Yii::app()->params->project?></h1>

<?php if( isset($top) ) echo $top?>

<table class="zero inner" width="98%">
  <tr>
    <td nowrap="nowrap" valign="top" class="hl1">Дата события:</td>
    <td width="99%"><?php echo date('Y.m.d'); ?></td>
  </tr>
  <tr>
    <td nowrap="nowrap" valign="top" class="hl1">Время события:</td>
    <td><?php echo date('H:i:s'); ?></td>
  </tr>
  <tr>
    <td nowrap="nowrap" valign="top" class="hl1">IP адрес:</td>
    <td><?php echo Yii::app()->request->userHostAddress; ?></td>
  </tr>
  <?php  if( !empty($fields) ) {?>
    <?php foreach($fields as $label => $value) {?>
      <tr>
        <td nowrap="nowrap" valign="top" class="hl1"><?php echo $label; ?>:</td>
        <td><?php echo $value; ?></td>
      </tr>
    <?php }?>
  <?php }?>
</table>

<?php if( isset($bottom) ) echo $bottom?>

<?php if( isset($adminUrl) ) { ?>

  <a href=<?php echo $adminUrl; ?>>Урл в админе</a>

<?php } ?>