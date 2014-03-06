<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.share
 *
 *  Компонент
 *
 * <pre>
 *  $curl = new Curl(array(
 *    'TimeOut' => $this->time_out,
 *    'Proxy'  => 'localhost:3128'
 *  );
 * </pre>
 */
class Curl extends CApplicationComponent
{
  public $result;

  private $ch;

  private $error;

  public function __construct(array $options = array())
  {
    if( !extension_loaded('curl') )
      throw new Exception('Curl module are not installed!');

    $this->ch = curl_init();
    curl_setopt($this->ch, CURLOPT_HEADER, (isset($options['header'])) ? true : false);
    curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($this->ch, CURLOPT_HTTPHEADER, array('Accept: text/plain', 'User-Agent: cURL'));

    foreach($options as $key => $value)
      if( method_exists($this, "set".$key) )
        call_user_func_array(array($this, "set".$key), $value);
  }

  public function init()
  {

  }

  /**
   * @param integer $timeOut
   */
  public function setTimeOut($timeOut = 30)
  {
    curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, $timeOut);
  }

  /**
   * @param string $proxy localhost:3128
   */
  public function setProxy($proxy)
  {
    curl_setopt($this->ch, CURLOPT_PROXY, $proxy);
  }

  /**
   * GET request
   *
   * @param string $url
   * @param array $getParams
   *
   * @return mixed
   */
  public function get($url, $getParams = array())
  {
    $getParams = http_build_query($getParams);
    $getParams = (preg_match("/\?/", $url) ? "&" : "?").$getParams;

    curl_setopt($this->ch, CURLOPT_POST, false);
    curl_setopt($this->ch, CURLOPT_URL, $url.$getParams);

    return $this->exec();
  }

  /**
   * POST request
   *
   * @param string $url
   * @param array $postParams
   *
   * @return mixed
   */
  public function post($url, $postParams = array())
  {
    curl_setopt($this->ch, CURLOPT_URL, $url);
    curl_setopt($this->ch, CURLOPT_POST, true);
    curl_setopt($this->ch, CURLOPT_POSTFIELDS, $postParams);

    return $this->exec();
  }

  private function exec()
  {
    $this->result = curl_exec($this->ch);
    $this->error  = curl_error($this->ch);

    return $this->result;
  }

  /**
   * @return string error
   *
   */
  public function getLastError()
  {
    return $this->error;
  }

  public function __destruct()
  {
    curl_close($this->ch);
  }
}