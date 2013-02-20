<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.banner
 */
class BannerModule extends BModule
{
  public $defaultController = 'BBanner';
  public $name = 'Баннеры';

  public function getUploadPath()
  {
    return Yii::app()->getFrontendPath().$this->defaultUploadDir.'images'.'/';
  }

  public function getUploadUrl()
  {
    return Yii::app()->getFrontendUrl().$this->defaultUploadDir.'images'.'/';
  }
}
