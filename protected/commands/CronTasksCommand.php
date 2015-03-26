<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package 
 */
Yii::import('frontend.share.helpers.*');
/**
 * Class CronTasksCommand
 */
class CronTasksCommand extends CConsoleCommand
{
  /**
   * @var array
   */
  private $commands = array();

  /**
   * @var string
   */
  private $commandPath;

  public function init()
  {
    $this->commandPath = Yii::getPathOfAlias('frontend').DIRECTORY_SEPARATOR.'yiic ';

    $configPath = Yii::getPathOfAlias('frontend.config.cron').'.php';
    if( file_exists($configPath) )
    {
      $this->setCommands($configPath);
    }
  }

  /**
   * @param $time
   */
  public function actionIndex($time)
  {
    $currentCommands = array();

    foreach($this->commands as $command => $commandTime)
    {
      if( $this->checkTime($time, $commandTime) )
        $currentCommands[] = $this->commandPath.$command;
    }

    echo $currentCommands ? implode(PHP_EOL, $currentCommands).PHP_EOL : '';
  }

  /**
   * @param string $configPath
   */
  private function setCommands($configPath)
  {
    $config = require($configPath);
    $commands = Yii::app()->getCommandRunner()->commands;

    foreach(Arr::get($config, 'commands', array()) as $command => $time)
    {
      $command = strtolower($command);

      if( isset($commands[$command]) )
        $this->commands[$command] = $this->parseTime($time);
    }
  }

  /**
   * @param string $time
   *
   * @return array
   */
  private function parseTime($time)
  {
    $times = explode(',', $time);

    array_walk($times, function(&$element) {
      $element = trim($element);
      if( is_numeric($element) && strlen($element) == 1 )
      {
        $element = '0'.$element;
      }
      $element = strval($element);
    });

    return $times;
  }

  /**
   * @param string $runTime
   * @param array $commandTimes
   *
   * @return bool
   */
  private function checkTime($runTime, array $commandTimes)
  {
    $hour = strval(DateTime::createFromFormat('H:i:s', $runTime)->format('H'));

    if( array_search('*', $commandTimes) !== false )
      return true;

    foreach($commandTimes as $commandTime)
    {
      if( $hour == $commandTime )
        return true;

      if( preg_match("/\/(\d+)/", $commandTime, $matches) )
      {
        if( $hour % $matches[1] == 0 )
          return true;
      }
    }

    return false;
  }
}