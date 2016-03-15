<?php
/**
 * @author Alexandr Kolobkov <kolobkov@shogo.ru>, Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.seo
 */
class SeoModule extends BModule
{
  public $defaultController = 'BSitemap';

  public $group = 'seo';

  public $name = 'Seo';

  public function getMenuControllers()
  {
    return array(
      'BMeta' => array(
        'label' => 'Мета теги',
        'menu' => array('BMetaRoute', 'BMetaMask'),
        'itemOptions' => array('class' => 'seopanel')
      ),
      'BCounters' => array(
        'label' => 'Счетчики',
        'menu' => array('BCounters'),
        'itemOptions' => array('class' => 'counters')
      ),
      'BLinkBlock' => array(
        'label' => 'Ссылочный блок',
        'menu' => array('BLinkBlock'),
        'itemOptions' => array('class' => 'linksblock')
      ),
      'BLink' => array(
        'label' => 'Каталог ссылок',
        'menu' => array('BLink', 'BLinkSection'),
        'itemOptions' => array('class' => 'links')
      ),
      'BRedirects' => array(
        'label' => 'Редиректы',
        'menu' => array('BRedirect'),
        'itemOptions' => array('class' => 'redirect')
      ),
      'BSitemap' => array(
        'label' => 'SitemapXML',
        'menu' => array('BSitemap', 'BSitemapExclusion'),
        'itemOptions' => array('class' => 'sitemap')
      ),
    );
  }
}
