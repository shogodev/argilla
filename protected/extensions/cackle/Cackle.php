<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package 
 */
Yii::import('ext.cackle.models.*');
Yii::import('ext.cackle.*');

class Cackle extends CApplicationComponent
{
  protected $siteId;

  protected $siteApiKey;

  protected $singleSignOn = false;

  public function init()
  {
    parent::init();

    $configPath = Yii::getPathOfAlias('frontend.config.cackle').'.php';
    if( file_exists($configPath) )
    {
      $config = require($configPath);
      $this->siteId = $config['siteId'];
      $this->siteApiKey = $config['siteApiKey'];

      if( isset($config['singleSignOn']) )
        $this->singleSignOn = $config['singleSignOn'];
    }
    else
    {
      throw new CHttpException('500', 'Не найден кофигурационный файл cackle.php в папке config');
    }
  }

  /**
   * @param CActiveRecord|string $channel
   * @param null $containerId
   */
  public function comments($channel, $containerId = null)
  {
    Yii::app()->controller->widget('CackleWidget', array(
      'widget' => CackleWidget::COMMENT,
      'siteId' => $this->siteId,
      'channel' => $channel,
      'container' => $containerId,
      'ssoAuth' => $this->singleSignOn ? $this->getSingleSignOnString() : '',
    ));
  }

  /**
   * @param $channel
   * @param null $containerId
   */
  public function reviews($channel, $containerId = null)
  {
    Yii::app()->controller->widget('CackleWidget', array(
      'widget' => CackleWidget::REVIEW,
      'siteId' => $this->siteId,
      'channel' => $channel,
      'container' => $containerId,
      'ssoAuth' => $this->singleSignOn ? $this->getSingleSignOnString() : array()
    ));
  }

  protected function getSingleSignOnString()
  {
    $user = array();

    if( !Yii::app()->user->isGuest )
    {
      /**
       * @link http://admin.cackle.me/help/integrating-sso
       */
      $user = array(
        'id' => Yii::app()->user->data->id,
        'name' => Yii::app()->user->profile->name,
        'email' => Yii::app()->user->getEmail(),
        'avatar' => Yii::app()->createAbsoluteUrl('userProfile/avatar', array('id' => Yii::app()->user->data->id)),
      );
    }
    $currentTime = time();

    $singleSignOn = array();
    $singleSignOn['userData'] = base64_encode(!empty($user) ? json_encode($user) : '{}');
    $singleSignOn['sign'] = md5($singleSignOn['userData'].$this->siteApiKey.$currentTime);
    $singleSignOn['timestamp'] = $currentTime;

    return implode(' ', $singleSignOn);
  }

  protected function setSiteId($siteId)
  {
    $this->siteId = $siteId;
  }
}