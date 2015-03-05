<?php
/**
 * This is the template for generating an action view file.
 * The following variables are available in this template:
 * @var CMysqlColumnSchema[] $columns
 */
?>
<?php echo "<?php\r\n"; ?>
return array(
  'class' => '<?php echo $cssClass?>',
<?php if( isset($layout) ) {?>
  'layout' => '<?php echo $layout?>',

<?php }?>
<?php if( isset($elementsLayout) ) {?>
  'elementsLayout' => '<?php echo $elementsLayout?>',

<?php }?>
  'elements' => array(
<?php
if( $columns ) {
  foreach($columns as $attribute => $column) {
    if( in_array($attribute, array(
      'id',
      'position',
      'visible',
    ))) continue;

    if( in_array($column->dbType, array(
      'timestamp',
      'tinyint(1)',
    )) ) continue;

  if( $attribute == 'phone' ) {?>
    '<?php echo $attribute?>' => array(
      'type' => 'tel',
    ),
<?php } else if( $column->dbType == 'text') {?>
    '<?php echo $attribute?>' => array(
      'type' => 'textarea',
    ),
<?php } else if( $column->dbType == 'int(10) unsigned') {?>
    '<?php echo $attribute?>' => array(
      'type' => 'text',
    ),
<?php } else {?>
    '<?php echo $attribute?>' => array(
      'type' => 'text',
    ),
<?php }
  }
}
?>
  ),

  'buttons' => array(
    'submit' => array(
      'type'  => 'submit',
      'class' => '<?php echo $buttonClass?>',
      'value' => '<?php echo $buttonName?>'
    ),
  ),
);
