<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.tests.components
 */
class THttpRequest extends CHttpRequest
{
  protected $_requestUri;

  public function setRequestUri($string)
  {
    $this->_requestUri = $string;
  }

  public function getRequestUri()
  {
    if( $this->_requestUri === null )
    {
      $this->_requestUri = parent::getRequestUri();
    }

    return $this->_requestUri;
  }

  public function setAjax(array $data, $method = 'POST')
  {
    $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
    $_SERVER['REQUEST_METHOD'] = $method;
    $_POST = $data;
  }

  public function clearAjax()
  {
    unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_POST = array();
  }

  public function redirect($url, $terminate = true, $statusCode = 302)
  {
    throw new TRedirectException(200, 'Location: '.$url, $statusCode);
  }
}