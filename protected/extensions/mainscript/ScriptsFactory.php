<?php
Yii::import('ext.mainscript.creators.*');
Yii::import('ext.mainscript.helpers.*');

/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package ext.mainscript
 */
class ScriptsFactory
{
  public $mode  = 'frontend';
  public $debug = false;

  protected $model;

  public function init()
  {
    switch( $this->mode )
    {
      case 'backend':
        $this->model = new PackedScriptCreator();
        break;
      case 'frontend':
      default:
        if( $this->debug )
          $this->model = new PackedScriptCreator();
        else
        {
          $this->model = new CompiledScriptCreator();

          // Если не существует скопилированного файла, то сохраняем работоспособность сайта и отдаём запакованный файл
          if( !file_exists($this->model->getScript(true)) )
            $this->model = new PackedScriptCreator();
        }
        break;
    }
  }

  public function getModel()
  {
    return $this->model;
  }
}
