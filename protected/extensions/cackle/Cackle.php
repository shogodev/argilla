<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
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

  public function init()
  {
    parent::init();

    $configPath = Yii::getPathOfAlias('frontend.config.cackle').'.php';
    if( file_exists($configPath) )
    {
      $config = require($configPath);
      $this->siteId = $config['siteId'];
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
      'container' => $containerId
    ));
  }

  protected function setSiteId($siteId)
  {
    $this->siteId = $siteId;
  }
}