<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.controllers
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

    switch( $this->errorCode )
    {
      case 404:
        $this->error404();
        break;
      case 402:
        $this->error402();
        break;
      default:
        $this->defaultError();
    }
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

  protected function defaultError()
  {
    if( !YII_DEBUG )
      $this->render('error');
    else
      $this->render('error'.$this->errorCode, $this->error);
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