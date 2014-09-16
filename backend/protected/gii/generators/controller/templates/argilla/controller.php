<?php
/**
 * This is the template for generating a controller class file.
 * The following variables are available in this template:
 * - $this: the ControllerCode object
 */
?>
<?php echo "<?php\n"; ?>
/**
 * @author ... <...@...>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.<?php echo $this->getModule()->getId(); ?>
 */
class <?php echo $this->controllerClass; ?> extends <?php echo $this->baseClass."\n"; ?>
{
  public $position = 10;

  public $name = '<?php echo str_replace("Controller", "", $this->getControllerClass()); ?>';

  public $modelClass = '<?php echo str_replace("Controller", "", $this->getControllerClass()); ?>';
}