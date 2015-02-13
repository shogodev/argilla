<?php echo "<?php\n"; ?>
/**
 * @author <?php echo isset(Yii::app()->params['author']) ? Yii::app()->params['author']."\n" : "... <...@...>\n"?>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-<?php echo date('Y')?> Shogo
 * @license http://argilla.ru/LICENSE
 */
class <?php echo $this->moduleClass; ?> extends BModule
{
  public $defaultController = 'B<?php echo ucfirst($this->moduleID); ?>';

  public $name = '<?php echo $this->moduleClass; ?>';
}