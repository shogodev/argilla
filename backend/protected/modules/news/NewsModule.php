<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.news
 */
class NewsModule extends BModule
{
  public $defaultController = 'BNews';
  public $name = 'Новости';

  public function getThumbsSettings()
  {
    return array('news' => array('pre' => array(100, 100)));
  }
}