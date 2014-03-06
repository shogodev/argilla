<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.controllers
 *
 * @property string $errorCode
 * @property string $errorMessage
 */
class ErrorController extends FController
{
  /**
   * @var int
   */
  protected $errorCode;

  /**
   * @var string
   */
  protected $errorMessage;

  /**
   * @var array
   */
  protected $error;

  public function actionError()
  {
    $this->initError();

    if( Yii::app()->request->isAjaxRequest )
    {
      echo $this->errorMessage;
      Yii::app()->end();
    }

    if( YII_DEBUG )
      $this->render('errorDebug', $this->error);
    else
    {
      $methodName = 'error'.$this->errorCode;

      if( method_exists($this, $methodName) )
        $this->$methodName();
      else
        $this->render('error');
    }
  }

  public function getErrorCode()
  {
    return $this->errorCode;
  }

  public function getErrorMessage()
  {
    return $this->errorMessage;
  }

  protected function error404()
  {
    $this->render('error404');
  }

  protected function error402()
  {
    $this->render('error402', array(
      'loginForm' => method_exists($this, 'getLoginForm') ? $this->getLoginForm() : null,
    ));
  }

  protected function initError()
  {
    if( !empty(Yii::app()->errorHandler->error) )
    {
      $this->error        = Yii::app()->errorHandler->error;
      $this->errorCode    = Yii::app()->errorHandler->error['code'];
      $this->errorMessage = Yii::app()->errorHandler->error['message'];
    }
  }
}