<?php
class SeoModule extends BModule
{
  public $defaultController = 'BMetaRoute';

  public $group = 'seo';

  public $name = 'Seo';

  public function init()
  {
    $this->setImport(
      array(
        'seo.models.*',
        'seo.components.*',
        'seo.controllers.*',
      ));
  }

  public function getMenuControllers()
  {
    return array(
      'BMeta'       => array('label' => 'Мета теги', 'url' => Yii::app()->createUrl('seo/BMetaRoute'), 'menu' => array('BMetaRoute', 'BMetaMask'), 'itemOptions' => array('class' => 'seopanel')),
      'BCounters'   => array('label' => 'Счетчики', 'url' => Yii::app()->createUrl('seo/BCounters'), 'menu' => array('BCounters'), 'itemOptions' => array('class' => 'counters')),
      'BLinksBlock' => array('label' => 'Ссылочный блок', 'url' => Yii::app()->createUrl('seo/BLinksBlock'), 'menu' => array('BLinksBlock'), 'itemOptions' => array('class' => 'linksblock')),
      'BLinks'      => array('label' => 'Каталог ссылок', 'url'   => Yii::app()->createUrl('seo/BLinks'), 'menu' => array('BLinks', 'BLinksSection'), 'itemOptions' => array('class' => 'links')),
      'BRedirects'  => array('label' => 'Редиректы', 'url' => Yii::app()->createUrl('seo/BRedirect'), 'menu' => array('BRedirects'), 'itemOptions' => array('class' => 'redirect')),
    );
  }
}
