<?php
/**
 * @link      https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license   http://argilla.ru/LICENSE
 */
class BSitemapController extends BController
{
  public $position = 10;

  public $name = 'Sitemap XML';

  public $modelClass = 'BSitemapRoute';

  public $moduleMenu = 'BSitemap';
}