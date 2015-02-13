<?php
/**
 * This is the template for generating a controller class file.
 * The following variables are available in this template:
 * - $this: the ControllerCode object
 */
?>
<?php echo "<?php\n"; ?>
/**
 * @author <?php echo isset(Yii::app()->params['author']) ? Yii::app()->params['author']."\n" : "... <...@...>\n"?>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-<?php echo date('Y')?> Shogo
 * @license http://argilla.ru/LICENSE
 */

/**
 * Class <?php echo $this->controllerClass."\n"?>
<?php if( isset($behaviors) ) {?>
<?php foreach($behaviors as $behavior) {?>
 * @mixin <?php echo $behavior."\n"?>
<?php }?>
<?php }?>
*/
class <?php echo $this->controllerClass; ?> extends <?php echo $this->baseClass."\n"; ?>
{
<?php if( isset($behaviors) ) {?>
  public function behaviors()
  {
    return CMap::mergeArray(parent::behaviors(), array(
  <?php foreach($behaviors as $behavior) {?>
    '<?php echo strtolower(substr($behavior, 0, 1)).substr($behavior, 1)?>' => array('class' => '<?php echo $behavior?>')
  <?php }?>
  ));
  }
<?php }?>
}