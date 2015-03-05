<?php echo "<?php\r\n"; ?>
/**
 * @author ... <...@...>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.<?php echo $this->moduleID; ?>
 */
class <?php echo $this->moduleClass; ?> extends BModule
{
  public $defaultController = 'B<?php echo ucfirst($this->moduleID); ?>';

  public $name = '<?php echo $this->moduleClass; ?>';
}