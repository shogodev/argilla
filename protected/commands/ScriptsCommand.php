<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.commands
 */

Yii::import('ext.mainscript.*');
Yii::import('ext.mainscript.helpers.*');
Yii::import('ext.mainscript.creators.*');

class ScriptsCommand extends CConsoleCommand
{
  private $scriptCreator;
  private $factory;

  /**
   * Создание на фронтенде склееного файла скриптов
   *
   * <pre>
   *  //console
   *  ./yiic scripts pack
   * </pre>
   *
   * @return void
   */
  public function actionPack()
  {
    echo "--------------------------------------------------------------\n";
    echo "\tФормирование packed.js" . "\n";
    echo "--------------------------------------------------------------\n";

    ScriptsFileFinder::$root = dirname(Yii::getPathOfAlias('frontend'));

    $this->factory = new ScriptsFactory();
    $this->factory->debug = true;
    $this->factory->mode  = 'frontend';
    $this->factory->init();

    $this->scriptCreator = $this->factory->getModel();
    $this->scriptCreator->delete();
    $this->scriptCreator->create();

    foreach( ScriptsFileFinder::getInstance()->getFiles() as $file )
    {
      echo "\tФайл: " . $file . "\n";
    }

    echo "\n--------------------------------------------------------------\n";
    echo "\t" . 'Файл скриптов packed.js успешно создан' . "\n";
    echo "--------------------------------------------------------------\n";
  }
}
