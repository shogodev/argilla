<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2016 Shogo
 * @license http://argilla.ru/LICENSE
 */
Yii::import('backend.modules.product.modules.import.components.ImportHelper');

class BBaseLogViewController extends BController
{
  public $enabled = false;

  public $name = '';

  public $logDirPath = 'protected/runtime';

  public $logFileName = 'application.log';

  public $showBy = 200;

  public function actionIndex()
  {
    $logPath = $this->getBasePath().ImportHelper::wrapInSlash($this->logDirPath).$this->logFileName;

    if( file_exists($logPath) )
    {
      $log = $this->formatLog(file($logPath));

      $log = array_slice(array_reverse($log), 0, $this->showBy);

      $dataLog = implode('', $log);
    }
    else
    {
      $dataLog = "Нет данных";
    }

    $this->render('index', array('dataLog' => $dataLog));
  }

  protected function formatLog($log)
  {
    $blockList = array();
    $block = '';
    $blockStarted = false;
    foreach($log as $line)
    {
      if( preg_match('/^(\d\d\d\d\/\d\d\/\d\d \d\d:\d\d:\d\d)(.*)/', $line) )
      {
        if( $blockStarted )
        {
          $block .= '</div>';
          $blockList[] = $block;
          $block = '';
          $blockStarted = false;
        }

        $blockStarted = true;
        $block .= $this->formatHeader($line);
        $block .= '<div class="log-body" style="display: none; padding-left: 15px; line-height: 1.5;">';
      }
      else
      {
        $block .= $line.'<br/>';
      }
    }

    if( !empty($block) )
    {
      $block .= '</div>';
      $blockList[] = $block;
    }

    return $blockList;
  }

  protected function formatHeader($text)
  {
    if( preg_match('/\[error\]/', $text) )
      $color = 'ffa799';
    else if( preg_match('/\[info\]/', $text) )
      $color = 'f5f5ff';
    else
      $color = 'fcfbc0';

    $header = '<div class="log-header" style="cursor: pointer; color: black; background-color: #'.$color.'; line-height: 2; padding-left: 7px; margin-bottom: 3px;">';
    if( preg_match('/^(\d\d\d\d\/\d\d\/\d\d \d\d:\d\d:\d\d)(.*)/', $text, $result) )
    {
      $header .= '<b>'.$result[1].'</b>';
      $header .= CHtml::encode($result[2]);
    }
    else
    {
      $header .= CHtml::encode($text);
    }
    $header .= '</div>';

    return $header;
  }

  protected function getBasePath()
  {
    return realpath(Yii::getPathOfAlias('frontend').'/..');
  }
}