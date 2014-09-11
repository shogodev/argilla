<?php
/**
 * @author Nikita Pimoshenko <pimoshenko@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.seo.controllers
 */
class BSitemapExclusionController extends BController
{
  public $position = 20;

  public $name = 'Исключения';

  public $modelClass = 'BSitemapExclusion';

  public $moduleMenu = 'BSitemap';
}