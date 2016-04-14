<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components
 *
 * @property FController $controller
 * @property SClientScript $clientScript
 * @property SAssetManager $assetManager
 * @property ScriptsFactory $mainscript
 * @property FWebUser $user
 * @property FUrlManager $urlManager
 * @property SFormatter $format
 * @property Email $email
 * @property SNotification $notification
 * @property RequestRedirectComponent $requestRedirect
 * @property EPhpThumb $phpThumb
 * @property Meta $meta
 * @property CAttributeCollection $params
 */
class FApplication extends CWebApplication
{
  /**
   * @param CController $controller
   * @param CAction $action
   *
   * @return bool
   */
  public function beforeControllerAction($controller, $action)
  {
    $this->getComponent('meta');
    $this->onBeforeControllerAction(new CEvent($this));

    return parent::beforeControllerAction($controller, $action);
  }

  public function onBeforeControllerAction(CEvent $event)
  {
    $this->raiseEvent('onBeforeControllerAction', $event);
  }

  protected function init()
  {
    parent::init();

    $this->setProjectName();
    $this->setMbEncoding();
  }

  protected function setProjectName()
  {
    if( empty($this->params->project) && isset($_SERVER['SERVER_NAME']) )
    {
      $this->params->project = preg_replace("/^www./", '', Yii::app()->request->getServerName());
    }
  }

  protected function setMbEncoding()
  {
    mb_internal_encoding("UTF-8");
    mb_http_output("UTF-8" );
  }
}