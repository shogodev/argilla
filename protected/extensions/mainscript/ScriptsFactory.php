<?php
Yii::import('ext.mainscript.creators.*');
Yii::import('ext.mainscript.helpers.*');

/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package ext.mainscript
 */
class ScriptsFactory
{
  public $mode  = 'frontend';

  public $debug = false;

  public $useGulp = false;

  protected $model;

  public function init()
  {
    switch( $this->mode )
    {
      case 'backend':
        $this->model = new PackedScriptCreator();
        $this->model->addScript('packed.js');
        break;
      case 'frontend':
      default:

        if( $this->useGulp )
        {
          $this->model = new DummyScriptCreator();
          $this->model->addScript('vendor.js');
          $this->model->addScript('common.js');
        }
        else if( $this->debug )
        {
          $this->model = new PackedScriptCreator();
          $this->model->addScript('packed.js');
        }
        else
        {
          $this->model = new CompiledScriptCreator();
          $this->model->addScript('compiled.js');

          // Если не существует скопилированного файла, то сохраняем работоспособность сайта и отдаём запакованный файл
          if( !$this->model->scripsExists() )
          {
            $this->model = new PackedScriptCreator();
            $this->model->addScript('packed.js');
          }
        }

        break;
    }
  }

  /**
   * @return ScriptAbstractCreator
   */
  public function getModel()
  {
    return $this->model;
  }
}
