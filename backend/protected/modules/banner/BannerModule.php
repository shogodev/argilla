<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.banner
 */
Yii::import('backend.components.BModule');

class BannerModule extends BModule
{
  public $defaultController = 'BBanner';

  public $name = 'Баннеры';

  public static $bannerLocations = array(
    'all' => 'Не важно', // Для доступа по url без привязки к локации
    'rotator' => 'Ротатор на главной',
    'catalog' => 'Каталог верх',
    'catalog_left' => 'Каталог слева',
  );

  public function getUploadPath()
  {
    return Yii::app()->getFrontendRoot().$this->defaultUploadDir.'images'.'/';
  }

  public function getUploadUrl()
  {
    return Yii::app()->getFrontendUrl().$this->defaultUploadDir.'images'.'/';
  }
}
