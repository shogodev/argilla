<?php
/**
 * This is the template for generating a controller class file.
 * The following variables are available in this template:
 * - $this: the ControllerCode object
 */
?>
<?php echo "<?php\r\n"; ?>
/**
 * @author <?php echo isset(Yii::app()->params['author']) ? Yii::app()->params['author']."\r\n" : "... <...@...>\r\n"?>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-<?php echo date('Y')?> Shogo
 * @license http://argilla.ru/LICENSE
 */
class <?php echo $this->controllerClass; ?> extends <?php echo $this->baseClass."\r\n"; ?>
{
  public $position = 10;

  public $name = '<?php echo isset($name) ? $name : str_replace("Controller", "", $this->getControllerClass()); ?>';

  public $modelClass = '<?php echo str_replace("Controller", "", $this->getControllerClass()); ?>';
}