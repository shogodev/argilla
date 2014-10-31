<?php
/**
 * @var array|null $fields
 * @var array|null $exceptedAttributes
 * @var CModel|null $model
 */
?>

<?php
  $modelFields = isset($fields) ? $fields : array();
  if( $model instanceof CModel )
  {
    foreach($model->attributeLabels() as $attribute => $label)
    {
      if( isset($exceptedAttributes) && !in_array($attribute, $exceptedAttributes ) )
        continue;

      if( !empty($model->$attribute) && !isset($modelFields[$label])  )
        $modelFields[$label] = $model->$attribute;
    }
  }
?>

<?php  if( !empty($modelFields) ) {?>
  <table width="98%">
      <?php foreach($modelFields as $label => $value) {?>
        <tr>
          <td nowrap="nowrap" valign="top" style="width: 150px"><?php echo $label; ?>:</td>
          <td><?php echo $value; ?></td>
        </tr>
      <?php }?>
  </table>
<?php }?>

