<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package ext.mainscript
 */
class SClientScript extends CClientScript
{
  /**
   * @param string $output
   *
   * @return void
   */
  public function render(&$output)
  {
    $this->prepareScripts();

    parent::render($output);
  }

  /**
   * Происходит очистка уже загруженных скриптов,
   * для того, чтобы в нужном порядке загрузить список скриптов
   *
   * По порядку идут:
   *  1) файлы скриптов из ядра Yii
   *  2) основной скрипт
   *  3) восстанавливаются остальные скрипты
   */
  private function prepareScripts()
  {
    //Clean total scripts
    $totalScripts      = $this->scriptFiles;
    $this->scriptFiles = array();

    $this->prepareCoreScripts();
    $this->prepareMainScript();
    $this->restoreScripts($totalScripts);

  }

  private function prepareMainScript()
  {
    $path = Yii::app()->mainscript->getModel()->getScript(true);
    $url  = Yii::app()->assetManager->publish($path, true);

    $this->registerScriptFile($url);
  }

  private function prepareCoreScripts()
  {
    if( Yii::app()->mainscript->mode === 'frontend' )
      $this->coreScripts = array();
  }

  /**
   * @param array $totalScripts
   */
  private function restoreScripts($totalScripts)
  {
    foreach( $totalScripts as $scriptList )
    {
      foreach( $scriptList as $script )
      {
        $this->registerScriptFile($script);
      }
    }
  }
}
