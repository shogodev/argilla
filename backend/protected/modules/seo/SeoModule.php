<?php
/**
 * @author Alexandr Kolobkov <kolobkov@shogo.ru>, Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.rbac
 */
class SeoModule extends BModule
{
  public $defaultController = 'BMetaRoute';
  public $group = 'seo';
  public $name = 'Seo';

  public function getMenuControllers()
  {
    return array(
      'BMeta'       => array('label' => 'Мета теги', 'url' => Yii::app()->createUrl('seo/BMetaRoute'), 'menu' => array('BMetaRoute', 'BMetaMask'), 'itemOptions' => array('class' => 'seopanel')),
      'BCounters'   => array('label' => 'Счетчики', 'url' => Yii::app()->createUrl('seo/BCounters'), 'menu' => array('BCounters'), 'itemOptions' => array('class' => 'counters')),
      'BLinkBlock' => array('label' => 'Ссылочный блок', 'url' => Yii::app()->createUrl('seo/BLinkBlock'), 'menu' => array('BLinkBlock'), 'itemOptions' => array('class' => 'linksblock')),
      'BLink'      => array('label' => 'Каталог ссылок', 'url'   => Yii::app()->createUrl('seo/BLink'), 'menu' => array('BLink', 'BLinkSection'), 'itemOptions' => array('class' => 'links')),
      'BRedirects'  => array('label' => 'Редиректы', 'url' => Yii::app()->createUrl('seo/BRedirect'), 'menu' => array('BRedirects'), 'itemOptions' => array('class' => 'redirect')),
    );
  }
}
