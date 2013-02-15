<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components
 */
class FApplication extends CWebApplication
{
  public function init()
  {
    parent::init();

    if( empty($this->params->project) )
      $this->params->project = preg_replace("/^www./", '', Yii::app()->request->serverName);

    $this->setMbEncoding();
  }

  protected function setMbEncoding()
  {
    mb_internal_encoding("UTF-8");
    mb_http_output("UTF-8" );
  }
}