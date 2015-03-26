<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package 
 */
Yii::import('frontend.components.ar.*');
Yii::import('frontend.share.helpers.*');
Yii::import('frontend.share.*');
Yii::import('ext.cackle.*');
Yii::import('ext.cackle.components.*');

class CackleSyncCommand extends CConsoleCommand
{
  public function beforeAction($action,$params)
  {
    Yii::app()->attachEventHandler('onError', array($this, 'onError'));
    Yii::app()->attachEventHandler('onException', array($this, 'onException'));

    return parent::beforeAction($action,$params);
  }

  public function actionIndex($mode = 'update')
  {
    $cackleReviews = new CackleSync(new CackleReviewManager());
    $cackleComments = new CackleSync(new CackleCommentManager());

    if( $mode === 'clear' )
    {
      if( !$this->confirm("Вы дейстивительно хотите очистить данные?".PHP_EOL."Все текущие записи будут потеряны!") )
        return;

      $cackleReviews->create();
      $cackleComments->create();
    }
    else
    {
      $cackleReviews->update();
      $cackleComments->update();
    }
  }

  /**
   * @param CExceptionEvent $event
   */
  public function onException($event)
  {
    $this->putInErrorStream($event->exception->getCode(), $event->exception->getMessage(), $event->exception->getLine(), $event->exception->getFile());
  }

  /**
   * @param CErrorEvent $event
   */
  public function onError($event)
  {
    $this->putInErrorStream($event->code, $event->message, $event->line, $event->file);
  }

  protected function putInErrorStream($code, $message, $line = null, $file = null)
  {
    $error = array('code: '.$code, 'message: '.$message);

    if( $line !== null )
      $error[] = 'line: '.$line;

    if( $file )
      $error[] = 'file: '.$file;

    file_put_contents("php://stderr", implode(PHP_EOL, $error).PHP_EOL);
  }
}